<template>
	<div id="item-detail-page">
		<vs-alert
			color="danger"
			title="Erro"
			:active.sync="error_occured"
		>
			<span>{{ error_msg }}.</span>
			<span>
				<span>Confira </span>
				<router-link
					:to="{name:'shop'}"
					class="text-inherit underline"
				>todos os itens</router-link>
			</span>
		</vs-alert>
		<vx-card
			v-if="item_data"
			:key="item_data.objectID"
		>
			<template slot="no-body">
				<div
					id="algolia-content-container"
					class="relative clearfix float-left"
				>
					<vs-sidebar
						class="items-no-padding subCategoriasMenu"
						parent="#algolia-content-container"
					>
						<div class="p-6 filter-container">
							<ais-hierarchical-menu ref="menuCategorias" :attributes="algoliaCategories" :limit="999">
								<div slot-scope="{
									items,
									refine,
									}">
									<ul>
										<li
											v-for="item in items"
											:key="item.value"
											class="flex items-center cursor-pointer py-1"
											@click="refineLocal(item)"
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
				</div>
				<div class="item-content mb-10 float-right">
					<div class="product-details">
						<div class="vx-row mx-0">
							<div class="vx-col md:w-2/5 w-full flex items-center justify-center p-0">
								<div class="product-img-container w-full mx-auto mb-10 md:mb-0">
									<img :src="item_data.image" :alt="item_data.name" class="responsive">
								</div>
							</div>
							<div class="vx-col md:w-3/5 w-full flex">
								<div class="vx-row w-full">
									<div class="vx-col w-full justify-center">
										<h3 class="vx-col w-full text-center item-title">{{ item_data.name }}</h3>
										<div class="w-full vx-col mb-4 flex item-properties">
											<div class="vx-col w-full flex justify-center item-color" v-if="item_data.cor && item_data.cor.length > 0">
												<div class="flex flex-wrap items-center mt-2 justify-center flex">
													<div
														:class="{'border-transparent': opt_color != color}"
														class="color-radio mx-1 border-2 border-solid cursor-pointer relative"
														:style="itemColor({color: color, style: ['borderColor']})"
														v-for="color in item_data.cor"
														:key="color"
														@click="opt_color=color"
													>
														<div
															class="h-6 w-6 absolute"
															:style="itemColor({color: color, style: ['backgroundColor']})"
														></div>
													</div>
												</div>
											</div>
											<div class="vx-col w-full flex justify-center item-quantity">
												<vs-select class="inline-flex flex justify-center" v-model="item_data.quantity">
													<vs-select-item :key="index" :value="item.value" :text="item.text" v-for="(item,index) in quantity" />
												</vs-select>
											</div>
										</div>
										<div class="vx-row flex justify-center">
											<div class="vx-col w-full flex justify-center">
												<vs-button
													type="filled"
													class="mb-4"
													icon-pack="feather"
													icon="icon-dollar-sign"
													icon-after
												>BUY NOW {{ item_data.price }}
												</vs-button>
											</div>
										</div>
										<div class="vx-row flex justify-center">
											<div class="vx-col w-full flex justify-center">
												<vs-button
													class="mb-4"
													type="border"
													v-if="!isInCart(item_data.objectID)"
													@click="toggleItemInCart(item_data)"
												>
													ADD TO CART
												</vs-button>
												<vs-button
													v-else
													type="border"
													class="mb-4"
													@click="toggleItemInCart(item_data)"
												>
													REMOVE
												</vs-button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="related-products text-center px-6" v-if="related_items && related_items.length > 0">
					<div class="related-headings mb-8 text-left">
						<h4>Related Itens:</h4>
					</div>
					<swiper
						:options="swiperOption"
						:dir="$vs.rtl ? 'rtl' : 'ltr'"
						:key="$vs.rtl"
						class="related-product-swiper px-12 py-6"
					>
						<swiper-slide
							v-for="item in related_items"
							:key="item.objectId"
							class="p-6 cursor-pointer"
						>
							<div class="img-container w-32 mx-auto my-base">
								<img
									class="responsive"
									:src="item.image"
									:alt="item.name"
								>
							</div>
							<div class="item-heading">
								<span class="item-name">{{ item.name }}</span>
								<span class="item-price">{{ item.price }} $</span>
							</div>
						</swiper-slide>
						<div
							class="swiper-button-prev"
							slot="button-prev"
						></div>
						<div
							class="swiper-button-next"
							slot="button-next"
						></div>
					</swiper>
				</div>
			</template>
		</vx-card>
	</div>
</template>

<script>
import "swiper/dist/css/swiper.min.css";
import { swiper, swiperSlide } from "vue-awesome-swiper";
import {
	AisHierarchicalMenu,
} from "vue-instantsearch";
import axios from "@/axios.js";
axios.defaults.withCredentials = true;

export default {
	components: {
		swiper,
		swiperSlide,
		AisHierarchicalMenu,
	},
	data() {
		return {
			algoliaCategories: [
				"hierarchicalCategories.lvl1",
			],
			algolia_index: {
				search(requests) {
					return axios
						.post(`/search`, requests)
						.then((response) => response.data);
				},
				getObject(id, callback) {
					return axios
						.get(`/produto/${id}`)
						.then((response) => callback(null, response.data))
						.catch((err) => callback(err, null));
				},
			},
			quantity:[
				{text:'1', value:1},
				{text:'2', value:2},
				{text:'3', value:3},
				{text:'4', value:4},
				{text:'5', value:5},
				{text:'6', value:6},
				{text:'7', value:7},
				{text:'8', value:8},
				{text:'9', value:9},
				{text:'10', value:10},
			],
			error_occured: false,
			error_msg: "",
			swiperOption: {
				slidesPerView: 5,
				spaceBetween: 55,
				breakpoints: {
					1600: {
						slidesPerView: 4,
						spaceBetween: 55,
					},
					1300: {
						slidesPerView: 3,
						spaceBetween: 55,
					},
					900: {
						slidesPerView: 2,
						spaceBetween: 55,
					},
					640: {
						slidesPerView: 1,
						spaceBetween: 55,
					},
				},
				navigation: {
					nextEl: ".swiper-button-next",
					prevEl: ".swiper-button-prev",
				},
			},
			opt_color: null,
			item_data: null,
			related_items: [],
		};
	},
	computed: {
		item_qty() {
			const item = this.$store.getters["eCommerce/getCartItem"](
				this.item_data.objectID
			);
			return Object.keys(item).length ? item.quantity : 1;
		},
		itemColor() {
			return (obj) => {
				let style_obj = {};

				obj.style.forEach((p) => {
					style_obj[p] = obj.color;
				});

				return style_obj;
			};
		},
		isInCart() {
			return (itemId) =>
				this.$store.getters["eCommerce/isInCart"](itemId);
		},
	},
	methods: {
		toggleItemInCart(item) {
			this.$store.dispatch("eCommerce/toggleItemInCart", item);
		},
		fetch_item_details(id) {
			this.algolia_index.getObject(id, (err, content) => {
				if (err) {
					this.error_occured = true;
					this.error_msg = err.message;
				} else {
					this.item_data = content.registro;
					if (content.related_items &&
						content.related_items.length > 0
					) {
						this.related_items = content.related_items;
					}
					if (this.item_data &&
						this.item_data.cor &&
						this.item_data.cor.length > 0
					) {
						this.opt_color = this.item_data.cor[0];
					}
					//WORKAROUND FOR TEST ONLY
					if (this.item_data &&
						this.item_data.categories &&
						this.item_data.categories.length &&
						this.item_data.categories[0]
					) {
						try {
							if (window.menuCategorias &&
								window.menuCategorias.state
							) {
								var cat = this.item_data.categories[0].toString();
								this.$nextTick(() => {
									setTimeout(() => {
										window.menuCategorias.state.refine(cat);
										this.$parent.$parent.afterEnter();
									}, 100);
								});
							} else{
								this.$nextTick(() => {
									this.$parent.$parent.afterEnter();
								});
							}
						} catch (err) {
							this.$nextTick(() => {
								this.$parent.$parent.afterEnter();
							});
						}
					} else {
						try {
							if (window.menuCategorias &&
								window.menuCategorias.state
							) {
								this.$nextTick(() => {
									setTimeout(() => {
										window.menuCategorias.state.refine('');
										this.$parent.$parent.afterEnter();
									}, 100);
								});
							} else {
								this.$nextTick(() => {
									this.$parent.$parent.afterEnter();
								});
							}
						} catch (err) {
							this.$nextTick(() => {
								this.$parent.$parent.afterEnter();
							});
						}
					}
				}
			});
		},
		refineLocal(item) {
			if (this.item_data &&
				this.item_data.categories &&
				this.item_data.categories.length &&
				this.item_data.categories[0]
			) {
				this.$router.push({
					name: "shop",
					params: {
						subCategoria: item.value,
					}
				});
			} else {
				this.$router.push({
					name: "shop",
					params: {
						subCategoria: item.value,
					}
				});
			}
		}
	},
	mounted() {
		try {
			if (window.menuSearch &&
				window.menuSearch.state
			) {
				window.menuSearch.state.refine('');
			}
		} catch (err) {}
		try {
			if (window.menuCategorias &&
				window.menuCategorias.state
			) {
				window.menuCategorias.state.refine('');
			}
		} catch (err) {}
		try {
			if (window.menuSubCategorias &&
				window.menuSubCategorias.state
			) {
				window.menuSubCategorias.state.refine('');
			}
		} catch (err) {}
		this.fetch_item_details(this.$route.params.item_id);
	}
};
</script>

<style lang="scss">
@import "@sass/vuexy/_variables.scss";

#item-detail-page {
	>div.vx-card {
		border: 0px;
		box-shadow: none;
		background: transparent;
		div.related-products {
			clear: both;
		}
		h3.item-title {
			margin-bottom: 260px;
		}
		button {
			border-radius: 0px;
		}
		#algolia-content-container {
			.vs-sidebar {
				position: relative;
				float: left;
			}
			div.subCategoriasMenu {
				display: initial !important;
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
	}
	.color-radio {
		height: 2.28rem;
		width: 2.28rem;

		> div {
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
		}
	}

	.product-sales-meta-list {
		.vs-list--icon {
			padding-left: 0;
		}
	}

	.related-product-swiper {
		// padding-right: 2rem;
		// padding-left: 2rem;

		.swiper-wrapper {
			padding-bottom: 2rem;

			> .swiper-slide {
				background-color: #f7f7f7;
				box-shadow: 0 4px 18px 0 rgba(0, 0, 0, 0.1),
					0 5px 12px 0 rgba(0, 0, 0, 0.08) !important;

				.theme-dark & {
					background-color: $theme-light-dark-bg;
				}
			}
		}

		.swiper-button-next,
		.swiper-button-prev {
			transform: scale(0.5);
			filter: hue-rotate(40deg);
		}

		.swiper-button-next {
			right: 0;
		}

		.swiper-button-prev {
			left: 0;
		}
	}
}
@media (max-width: 1056px) {
	#item-detail-page {
		>div.vx-card {
			#algolia-content-container {
				div.subCategoriasMenu {
					display: none !important;
				}
			}
		}
	}
}
@media (max-width: 1023px) {
	#item-detail-page {
		>div.vx-card {
			div.item-properties {
				display: block !important;
				div.item-color {
					margin-bottom: 10px;
				}
			}
			h3.item-title {
				margin-bottom: 160px;
			}
		}
	}
}
@media (max-width: 768px) {
	#item-detail-page {
		>div.vx-card {
			h3.item-title {
				margin-bottom: 80px;
			}
		}
	}
}
@media (max-width: 767px) {
	#item-detail-page {
		>div.vx-card {
			h3.item-title {
				margin-bottom: 10px;
			}
		}
	}
}
</style>
