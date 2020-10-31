<template>
	<div class="parentx">
		<vs-sidebar
			class="v-nav-menu items-no-padding v-nav-menu-wrapper-vertical"
			v-model="isVerticalNavMenuActive"
			ref="verticalNavMenu"
			default-index="-1"
			:click-not-close="clickNotClose"
			:reduce-not-rebound="reduceNotRebound"
			:parent="parent"
			:hiddenBackground="clickNotClose"
			:reduce="reduce"
			v-hammer:swipe.left="onSwipeLeft"
		>
			<div
				@mouseenter="mouseEnter"
				@mouseleave="mouseLeave"
			>
				<div
					class="header-sidebar flex items-end justify-between"
					slot="header"
				>
					<div class="vx-logo flex items-center">
						<logo class="logoMobile" :onClick="goToIndex" />
					</div>
					<div class="v-nav-menu-close-wrapper">
						<template v-if="showCloseButton">
							<feather-icon
								icon="XIcon"
								class="m-0 cursor-pointer text-dark"
								@click="$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', false)"
							/>
						</template>
						<template v-else-if="!showCloseButton && !verticalNavMenuItemsMin">
							<feather-icon
								id="btnVNavMenuMinToggler"
								class="mr-0 cursor-pointer text-dark"
								:icon="reduce ? 'CircleIcon' : 'DiscIcon'"
								svg-classes="stroke-current text-dark"
								@click="toggleReduce(!reduce)"
							/>
						</template>
					</div>
				</div>
				<ais-hierarchical-menu
					:attributes="algoliaCategories"
					ref="menuCategorias"
					class="hidden"
				>
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
									class="ml-2"
									:class="{'text-primary': item.isRefined}"
								>{{ item.label }}</span>
							</li>
						</ul>
					</div>
				</ais-hierarchical-menu>
				<ais-hierarchical-menu
					:attributes="algoliaSubCategories"
					ref="menuSubCategorias"
					class="hidden"
				>
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
									class="ml-2"
									:class="{'text-primary': item.isRefined}"
								>{{ item.label }}</span>
							</li>
						</ul>
					</div>
				</ais-hierarchical-menu>
				<VuePerfectScrollbar
					ref="verticalNavMenuPs"
					class="scroll-area-v-nav-menu pt-2"
					:settings="settings"
					:key="$vs.rtl"
				>
					<div class="categoriasMenu mt-5">
						<ul>
							<li
								v-for="item in listaCategorias"
								:key="item.id"
								class="items-center mb-5"
							>
								<span
									class="ml-2 cursor-pointer"
									@click="refineCategoria(item.nome)"
									:class="{'text-primary': isRefined(item.nome)}"
								>{{ item.nome }}</span>
								<v-nav-menu-sub-categoria
									ref="vnavSubCategoria"
									class="mt-2"
									:refineSubCategoria="refineSubCategoria"
									:isRefined="isRefined"
									:isSubRefined="isSubRefined"
									:categorias="item.subcategorias"
									:categoria="item.nome"
								/>
							</li>
						</ul>
					</div>
				</VuePerfectScrollbar>
			</div>
		</vs-sidebar>
		<div
			v-if="!isVerticalNavMenuActive"
			class="v-nav-menu-swipe-area"
			v-hammer:swipe.right="onSwipeAreaSwipeRight"
		/>
	</div>
</template>
<script>
import VuePerfectScrollbar from "vue-perfect-scrollbar";
import Logo from "../Logo.vue";
import VNavMenuSubCategoria from "./VerticalNavMenuSubCategoria.vue";
import { AisHierarchicalMenu } from "vue-instantsearch";
import axios from "@/axios.js";
axios.defaults.withCredentials = true;

export default {
	name: "v-nav-menu",
	components: {
		VuePerfectScrollbar,
		AisHierarchicalMenu,
		Logo,
		VNavMenuSubCategoria,
	},
	props: {
		logo: { type: String },
		openGroupHover: { type: Boolean, default: false },
		parent: { type: String },
		reduceNotRebound: { type: Boolean, default: true },
		navMenuItems: { type: Array, required: true },
		title: { type: String },
	},
	data: () => ({
		clickNotClose: false, // disable close navMenu on outside click
		isMouseEnter: false,
		reduce: false, // determines if navMenu is reduce - component property
		showCloseButton: false, // show close button in smaller devices
		settings: {
			// perfectScrollbar settings
			maxScrollbarLength: 60,
			wheelSpeed: 1,
			swipeEasing: true,
		},
		algoliaCategories: ["hierarchicalCategories.lvl0"],
		algoliaSubCategories: ["hierarchicalCategories.lvl1"],
		listaCategorias: [],
		categoria: null,
		subCategoria: null,
	}),
	computed: {
		isVerticalNavMenuActive: {
			get() {
				return this.$store.state.isVerticalNavMenuActive;
			},
			set(val) {
				this.$store.commit("TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE", val);
			},
		},
		layoutType() {
			return this.$store.state.mainLayoutType;
		},
		reduceButton: {
			get() {
				return this.$store.state.reduceButton;
			},
			set(val) {
				this.$store.commit("TOGGLE_REDUCE_BUTTON", val);
			},
		},
		isVerticalNavMenuReduced() {
			return Boolean(this.reduce && this.reduceButton);
		},
		verticalNavMenuItemsMin() {
			return this.$store.state.verticalNavMenuItemsMin;
		},
		windowWidth() {
			return this.$store.state.windowWidth;
		},
	},
	watch: {
		$route() {
			if (this.isVerticalNavMenuActive && this.showCloseButton)
				this.$store.commit("TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE", false);
		},
		reduce(val) {
			const verticalNavMenuWidth = val
				? "reduced"
				: this.$store.state.windowWidth < 1024
				? "no-nav-menu"
				: "default";
			this.$store.dispatch(
				"updateVerticalNavMenuWidth",
				verticalNavMenuWidth
			);

			setTimeout(function () {
				window.dispatchEvent(new Event("resize"));
			}, 100);
		},
		layoutType() {
			this.setVerticalNavMenuWidth();
		},
		reduceButton() {
			this.setVerticalNavMenuWidth();
		},
		windowWidth() {
			this.setVerticalNavMenuWidth();
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
		refineCategoria(nome) {
			if (! this.$route ||
				! this.$route.name ||
				this.$route.name != 'shop-item-detail-view'
			) {
				if (this.subCategoria) {
					this.subCategoria = null;
					this.$refs.menuSubCategorias.state.refine("");
				}
				if (this.isRefined(nome)) {
					this.categoria = null;
					this.$refs.menuCategorias.state.refine("");
				} else {
					this.categoria = nome;
					this.$refs.menuCategorias.state.refine(nome);
				}
			} else {
				if (this.subCategoria) {
					this.subCategoria = null;
					this.$refs.menuSubCategorias.state.refine("");
				}
				this.categoria = nome;
				if (this.$route &&
					this.$route.params &&
					this.$route.params.categoria == nome
				) {
					this.$router.push({
						name: "shop",
					});
				} else {
					this.$router.push({
						name: "shop",
						params: {
							categoria: nome,
						}
					});
				}
			}
		},
		refineSubCategoria(nome, categoria, comp) {
			if (! this.$route ||
				! this.$route.name ||
				this.$route.name != 'shop-item-detail-view'
			) {
				if (this.isSubRefined(nome, categoria)) {
					this.categoria = null;
					this.subCategoria = null;
					this.$refs.menuCategorias.state.refine("");
					this.$refs.menuSubCategorias.state.refine("");
				} else {
					this.categoria = categoria;
					this.subCategoria = nome;
					this.$refs.menuCategorias.state.refine(categoria);
					this.$refs.menuSubCategorias.state.refine(nome);
				}
			} else {
				this.categoria = categoria;
				this.subCategoria = nome;
				if (this.$route &&
					this.$route.params &&
					this.$route.params.categoria == categoria
				) {
					if (this.$route &&
						this.$route.params &&
						this.$route.params.subCategoria == nome
					) {
						this.$router.push({
							name: "shop",
						});
					} else {
						this.$refs.menuSubCategorias.state.refine(nome);
						this.$router.push({
							name: "shop",
							params: {
								subCategoria: nome,
							}
						});
					}
				} else {
					if (this.$route &&
						this.$route.params &&
						this.$route.params.subCategoria == nome
					) {
						this.$router.push({
							name: "shop",
							params: {
								categoria: categoria,
							}
						});
					} else {
						this.$router.push({
							name: "shop",
							params: {
								categoria: categoria,
								subCategoria: nome,
							}
						});
					}
				}
			}
			comp.$forceUpdate();
		},
		isRefined(nome) {
			return nome == this.categoria ? true : false;
		},
		isSubRefined(nome, categoria) {
			if (nome == this.subCategoria && categoria == this.categoria) {
				return true;
			}
			return false;
		},
		onSwipeLeft() {
			if (this.isVerticalNavMenuActive && this.showCloseButton)
				this.isVerticalNavMenuActive = false;
		},
		onSwipeAreaSwipeRight() {
			if (!this.isVerticalNavMenuActive && this.showCloseButton)
				this.isVerticalNavMenuActive = true;
		},
		mouseEnter() {
			if (this.reduce)
				this.$store.commit("UPDATE_VERTICAL_NAV_MENU_ITEMS_MIN", false);
			this.isMouseEnter = true;
		},
		mouseLeave() {
			if (this.reduce)
				this.$store.commit("UPDATE_VERTICAL_NAV_MENU_ITEMS_MIN", true);
			this.isMouseEnter = false;
		},
		setVerticalNavMenuWidth() {
			if (this.windowWidth > 1024) {
				if (this.layoutType === "vertical") {
					// Set reduce
					this.reduce = this.reduceButton ? true : false;

					// Open NavMenu
					this.$store.commit(
						"TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE",
						true
					);

					// Set Menu Items Only Icon Mode
					const verticalNavMenuItemsMin =
						this.reduceButton && !this.isMouseEnter ? true : false;
					this.$store.commit(
						"UPDATE_VERTICAL_NAV_MENU_ITEMS_MIN",
						verticalNavMenuItemsMin
					);

					// Menu Action buttons
					this.clickNotClose = true;
					this.showCloseButton = false;

					const verticalNavMenuWidth = this.isVerticalNavMenuReduced
						? "reduced"
						: "default";
					this.$store.dispatch(
						"updateVerticalNavMenuWidth",
						verticalNavMenuWidth
					);

					return;
				}
			}

			// Close NavMenu
			this.$store.commit("TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE", false);

			// Reduce button
			if (this.reduceButton) this.reduce = false;

			// Menu Action buttons
			this.showCloseButton = true;
			this.clickNotClose = false;

			// Update NavMenu Width
			this.$store.dispatch("updateVerticalNavMenuWidth", "no-nav-menu");

			// Remove Only Icon in Menu
			this.$store.commit("UPDATE_VERTICAL_NAV_MENU_ITEMS_MIN", false);
		},
		toggleReduce(val) {
			this.reduceButton = val;
			this.setVerticalNavMenuWidth();
		},
	},
	mounted() {
		this.setVerticalNavMenuWidth();
		window.menuCategorias = this.$refs.menuCategorias;
		window.menuSubCategorias = this.$refs.menuSubCategorias;
		axios.post("/categorias").then((response) => {
			if (
				response &&
				response.status == 200 &&
				response.data &&
				response.data instanceof Object
			) {
				if (
					Object.prototype.hasOwnProperty.call(
						response.data,
						"categorias"
					) &&
					response.data.categorias &&
					response.data.categorias instanceof Array
				) {
					this.listaCategorias = response.data.categorias;
				}
			}
		});
	},
};
</script>
<style lang="scss">
@import "@sass/vuexy/components/verticalNavMenu.scss";
div.v-nav-menu-wrapper-vertical {
	div.v-nav-menu-close-wrapper > span.feather-icon {
		top: 5px;
		left: 2px;
	}
	img.logoMobile {
		max-width: 145px;
	}
	div.vs-sidebar {
		max-width: 200px;
	}
	div.categoriasMenu > ul {
		margin-left: 30px;
		li > span.text-primary {
			font-weight: 600;
		}
		li > div > ul {
			margin-left: 20px;
			li > span.text-primary {
				font-weight: 500;
			}
		}
	}
	div.header-sidebar .feather-icon svg {
		color: rgba(var(--vs-dark), 1) !important;
	}
}
</style>
