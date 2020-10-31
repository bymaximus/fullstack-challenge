<template>
	<div>
		<ais-configure :hits-per-page.camel="9" />

		<div
			id="algolia-content-container"
			class="relative clearfix"
		>
			<vs-sidebar
				class="items-no-padding subCategoriasMenu"
				parent="#algolia-content-container"
			>
				<div class="p-6 filter-container">
					<ais-hierarchical-menu ref="aisMenu" :attributes="algoliaCategories" :limit="999">
						<div slot-scope="{
                              items,
                              refine,
                            }">
							<ul>
								<li
									v-for="item in items"
									:key="item.value"
									class="flex items-center cursor-pointer py-1"
									@click="refine(item.value)"
								>
									<span
										:class="{'text-primary': item.isRefined}"
									>{{ item.label }}</span>
								</li>
							</ul>
						</div>
					</ais-hierarchical-menu>
				</div>
			</vs-sidebar>
			<div class="sidebarspacerwithmargin">
				<ais-hits>
					<div slot-scope="{ items }">
						<div class="items-grid-view vx-row match-height">
							<div
								class="vx-col lg:w-1/3 sm:w-1/2 w-full"
								v-for="item in items"
								:key="item.objectID"
							>
								<item-grid-view :item="item">
									<template slot="action-buttons">
										<div class="flex flex-wrap">
											<div
												class="item-view-primary-action-btn p-3 flex flex-grow items-center justify-center cursor-pointer"
												@click="cartButtonClicked(item)"
											>
												<feather-icon
													icon="ShoppingBagIcon"
													svgClasses="h-4 w-4"
												/>
												<span
													class="text-sm font-semibold ml-2"
													v-if="isInCart(item.objectID)"
												>REMOVE</span>
												<span
													class="text-sm font-semibold ml-2"
													v-else
												>ADD TO CART</span>
											</div>
											<div
												class="item-view-secondary-action-btn bg-primary p-3 flex flex-grow items-center justify-center text-white cursor-pointer"
											>
												<feather-icon
													icon="DollarSignIcon"
													svgClasses="h-4 w-4"
												/>
												<span
													class="text-sm font-semibold ml-2"
												>BUY NOW</span>
											</div>
										</div>
									</template>
								</item-grid-view>
							</div>
						</div>
					</div>
				</ais-hits>
				<ais-pagination>
					<div slot-scope="{
                                currentRefinement,
                                nbPages,
                                pages,
                                isFirstPage,
                                isLastPage,
                                refine,
                                createURL
                            }">
						<vs-pagination
							:total="nbPages"
							:max="7"
							:value="currentRefinement + 1"
							@input="(val) => { refine(val - 1) }"
						/>
					</div>
				</ais-pagination>
			</div>
		</div>
	</div>
</template>

<script>
import {
	AisConfigure,
	AisHierarchicalMenu,
	AisHits,
	AisPagination,
} from "vue-instantsearch";
import axios from "@/axios.js";
axios.defaults.withCredentials = true;

export default {
	components: {
		ItemGridView: () => import("./components/ItemGridView.vue"),
		AisConfigure,
		AisHierarchicalMenu,
		AisHits,
		AisPagination,
	},
	data() {
		return {
			algoliaCategories: [
				"hierarchicalCategories.lvl1",
			],
		};
	},
	computed: {
		isInCart() {
			return (itemId) =>
				this.$store.getters["eCommerce/isInCart"](itemId);
		},
	},
	methods: {
		additemInCart(item) {
			this.$store.dispatch("eCommerce/additemInCart", item);
		},
		delItemInCart(item) {
			this.$store.dispatch("eCommerce/toggleItemInCart", item);
		},
		cartButtonClicked(item) {
			if (!this.isInCart(item.objectID)) {
				this.additemInCart(item);
			} else {
				this.delItemInCart(item);
			}
		},
	},
	mounted() {
		//WORKAROUND FOR TEST ONLY
		if (this.$route &&
			this.$route.params
		) {
			var closeLoad = true;
			var refineSub = true;
			if (this.$route.params.categoria) {
				try {
					if (window.menuCategorias &&
						window.menuCategorias.state
					) {
						var cat = this.$route.params.categoria;
						if (this.$route.params.subCategoria) {
							try {
								if (window.menuSubCategorias &&
									window.menuSubCategorias.state
								) {
									var subCat = this.$route.params.subCategoria;
									closeLoad = false;
									refineSub = false;
									this.$nextTick(() => {
										setTimeout(() => {
											window.menuCategorias.state.refine(cat);
											window.menuSubCategorias.state.refine(subCat);
											this.$parent.$parent.afterEnter();
										}, 100);
									});
								} else {
									closeLoad = false;
									this.$nextTick(() => {
										setTimeout(() => {
											window.menuCategorias.state.refine(cat);
											this.$parent.$parent.afterEnter();
										}, 100);
									});
								}
							} catch (err) {
								closeLoad = false;
								this.$nextTick(() => {
									setTimeout(() => {
										window.menuCategorias.state.refine(cat);
										this.$parent.$parent.afterEnter();
									}, 100);
								});
							}
						} else {
							closeLoad = false;
							this.$nextTick(() => {
								setTimeout(() => {
									window.menuCategorias.state.refine(cat);
									this.$parent.$parent.afterEnter();
								}, 100);
							});
						}
					}
				} catch (err) {
				}
			}
			if (this.$route.params.subCategoria && refineSub) {
				try {
					if (window.menuSubCategorias &&
						window.menuSubCategorias.state
					) {
						var subCat = this.$route.params.subCategoria;
						closeLoad = false;
						this.$nextTick(() => {
							setTimeout(() => {
								window.menuSubCategorias.state.refine(subCat);
								this.$parent.$parent.afterEnter();
							}, 100);
						});
					}
				} catch (err) {}
			}
			if (closeLoad) {
				this.$nextTick(() => {
					this.$parent.$parent.afterEnter();
				});
			}
		}
	}
};
</script>
<style lang="scss">
#algolia-instant-search-demo {
	.algolia-header {
		.algolia-filters-label {
			width: calc(260px + 2.4rem);
		}
	}

	#algolia-content-container {
		.vs-sidebar {
			position: relative;
			float: left;
		}
	}

	.algolia-search-input-right-aligned-icon {
		padding: 1rem 1.5rem;
	}

	.algolia-price-slider {
		min-width: unset;
	}

	.item-view-primary-action-btn {
		color: #2c2c2c !important;
		background-color: #f6f6f6;
		min-width: 50%;
	}

	.item-view-secondary-action-btn {
		min-width: 50%;
	}
}

.theme-dark {
	#algolia-instant-search-demo {
		#algolia-content-container {
			.vs-sidebar {
				background-color: #10163a;
			}
		}
	}
}

#algolia-content-container {
	div.subCategoriasMenu {
		div.vs-sidebar--background {
			display: none;
		}
		div.vs-sidebar {
			background-color: #FCFAFB;
			-webkit-box-shadow: none;
			box-shadow: none;
			-webkit-transition: none;
			ul {
				li {
					span {
						font-weight: 600;
					}
				}
			}
		}
		div.ais-HierarchicalMenu ul li span {
			width: 100%;
			text-align: center;
			color: #020202;
		}
	}
}
@media (max-width: 1023px) {
	div.subCategoriasMenu {
		display: none;
	}
}
@media (min-width: 1024px) {
	.main-horizontal.navbar-static .router-content {
		margin-top: 1rem;
	}
	#algolia-content-container {
		div.subCategoriasMenu {
			display: initial !important;
		}
		div.sidebarspacerwithmargin {
			margin-left: calc(260px + 2.2rem);
			width: calc(100% - 260px - 2.2rem);
		}

	}
}
</style>

