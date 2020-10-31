<template>
	<div class="relative">
		<div class="vx-navbar-wrapper navbar-full p-0 vx-navbar-wrapper-horizontal">
			<vs-navbar
				class="navbar-custom navbar-skelton text-dark"
				:class="navbarClasses"
				:style="navbarStyle"
				:color="navbarColor"
			>
				<div
					class="vx-logo flex items-left logo-wrapper"
				>
					<logo class="fill-current text-primary logo" :onClick="goToIndex" />
				</div>
				<ais-hierarchical-menu :attributes="algoliaCategories" ref="menuCategorias">
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
				<ais-search-box ref="menuSearch">
					<div slot-scope="{ currentRefinement, isSearchStalled, refine }">
						<div class="relative">
							<vs-input
								class="w-full vs-input-shadow-drop d-theme-input-dark-bg"
								:class="{
									'showSearch': showFullSearch
								}"
								v-model="currentRefinement"
								ref="searchInput"
								@input="refine($event)"
								@keyup.esc="refine('');showFullSearch=false;"
								@keyup.enter="showFullSearch=false;"
								@focus="showFullSearch = true"
								@blur="showFullSearch = false"
								size="large"
							/>
							<div
								slot="submit-icon"
								class="cursor-pointer searchLupa"
								:class="{
									'hidden': showFullSearch
								}"
								v-show="!currentRefinement"
								@click="showFullSearch = true"
							>
								<feather-icon
									icon="SearchIcon"
									svgClasses="h-6 w-6"
								/>
							</div>
							<div
								slot="reset-icon"
								class="cursor-pointer searchClear"
								v-show="currentRefinement"
							>
								<feather-icon
									icon="XIcon"
									svgClasses="h-6 w-6 cursor-pointer"
									@click="refine('');showFullSearch=true"
								/>
							</div>
						</div>
					</div>
				</ais-search-box>
			</vs-navbar>
			<div class="cart-wrapper flex text-dark">
				<div class="text-left flex text-dark items-center">
					<span class="text-dark">{{ cartItems.length }} ite<span v-show="cartItems.length > 1 || cartItems.length == 0">ns</span><span v-show="cartItems.length == 1">m</span></span>
				</div>
				<cart-drop-down />
			</div>
		</div>
	</div>
</template>

<script>
import Logo from "../Logo.vue";
import CartDropDown from "./components/CartDropDown.vue";
import {
	AisSearchBox,
	AisHierarchicalMenu,
} from "vue-instantsearch";

export default {
	name: "the-navbar-horizontal",
	props: {
		logo: { type: String },
		navbarType: {
			type: String,
			required: true,
		},
	},
	components: {
		Logo,
		CartDropDown,
		AisHierarchicalMenu,
		AisSearchBox,
	},
	data() {
		return {
			showFullSearch: false,
			algoliaCategories: [
				"hierarchicalCategories.lvl0",
			],
		};
	},
	watch: {
		showFullSearch(val) {
			if (val) {
				this.$nextTick(() => {
					setTimeout(() => {
						window
							.jQuery(this.$refs.searchInput.$refs.vsinput)
							.trigger("focus");
					}, 150);
				});
			}
		}
	},
	mounted() {
		window.menuCategorias = this.$refs.menuCategorias;
		window.menuSearch = this.$refs.menuSearch;
	},
	computed: {
		cartItems() {
			return this.$store.state.eCommerce.cartItems.slice().reverse();
		},
		navbarColor() {
			return "#FCFAFB";
		},
		isThemedark() {
			return this.$store.state.theme;
		},
		navbarStyle() {
			return this.navbarType === "static"
				? { transition: "all .25s ease-in-out" }
				: {};
		},
		navbarClasses() {
			return this.scrollY > 5 && this.navbarType === "static"
				? null
				: "d-theme-dark-light-bg shadow-none";
		},
		scrollY() {
			return this.$store.state.scrollY;
		},
		verticalNavMenuWidth() {
			return this.$store.state.verticalNavMenuWidth;
		},
		windowWidth() {
			return this.$store.state.windowWidth;
		},
	},
	methods: {
		goToIndex() {
			if (this.$route &&
				this.$route.name &&
				this.$route.name != 'shop'
			) {
				this.$router.push({
					name: "shop",
				});
			}
		},
		refineLocal(item) {
			if (! this.$route ||
				! this.$route.name ||
				this.$route.name != 'shop-item-detail-view'
			) {
				this.$refs.menuCategorias.state.refine(item.value);
			} else {
				if (this.$route &&
					this.$route.params &&
					this.$route.params.categoria == item.value
				) {
					this.$router.push({
						name: "shop",
					});
				} else {
					this.$router.push({
						name: "shop",
						params: {
							categoria: item.value,
						}
					});
				}
			}
		}
	}
};
</script>
<style lang="scss">
.vx-navbar-wrapper-horizontal {
	div.cart-wrapper {
		justify-content: flex-end;
		padding-right: 80px;
		padding-top: 3px;
	}
	div.vs-con-items {
		justify-content: space-between;
	}
	div.ais-HierarchicalMenu {
		width: 100%;
		text-align: center;
		margin-left: 20px;
		margin-right: 20px;
	}
	div.ais-HierarchicalMenu ul {
		justify-content: space-around;
		max-width: 500px;
		margin: auto;
		display: -webkit-box !important;
		display: flex !important;
	}
	div.ais-HierarchicalMenu ul li {
		display: inline !important;
		font-weight: 600;
	}
	div.ais-HierarchicalMenu ul li span {
		color: #020202;
	}
	div.ais-SearchBox {
		div.vs-component .vs-inputx {
			transition: none;
		}
		div.vs-component:not(.showSearch) .vs-inputx {
			padding: 2px 7px !important;
			border: 1px solid rgba(0, 0, 0, 0.6);
			border-radius: 23px;
			padding-left: 24px !important;
			padding-right: 0px !important;
		}
		div.vs-component.showSearch {
			display: block !important;
			position: fixed;
			top: 0px;
			left: 0px;
			right: 0px;
			bottom: 0px;
			z-index: 2;
			.vs-inputx {
				border-top-left-radius: 0px;
				border-top-right-radius: 0px;
			}
		}
		div.searchLupa {
			position: absolute;
			top: 2px;
			left: 4px;
		}
		div.searchClear {
			position: absolute;
			top: 3px;
			left: 6px;
		}
	}
	div.logo-wrapper {
		margin: auto;
		width: 100%;
		justify-content: flex-start;
		img.logo {
			margin-left: 60px;
			height: 20px;
		}
	}
}
</style>
