
<template>
	<div
		class="layout--main"
		:class="[layoutTypeClass, navbarClasses, footerClasses, {'no-scroll': isAppPage}]"
	>
		<ais-instant-search
			:search-client="searchClient"
			index-name="instant_search"
			id="algolia-instant-search-demo"
			ref="algoliaSearch"
		>
			<v-nav-menu
				:navMenuItems="navMenuItems"
				:title="menuTitle"
				parent=".layout--main"
			/>

			<div
				id="content-area"
				class="vs-con-loading__container"
				:class="[contentAreaClass, {'show-overlay': bodyOverlay}]"
			>
				<div id="content-overlay" />

				<template v-if="mainLayoutType === 'horizontal' && windowWidth >= 1024">
					<the-navbar-horizontal
						:navbarType="navbarType"
						:class="[
							{'text-white' : isNavbarDark  && !isThemeDark},
							{'text-base'  : !isNavbarDark && isThemeDark}
						]"
					/>
				</template>

				<template v-else>
					<the-navbar-vertical
						:navbarColor="navbarColor"
						:class="[
							{'text-white' : isNavbarDark  && !isThemeDark},
							{'text-base'  : !isNavbarDark && isThemeDark}
						]"
					/>
				</template>

				<div class="content-wrapper">
					<div
						class="router-view vs-con-loading__container"
						id="router-view"
					>
						<div
							class="router-content vs-con-loading__container"
							id="router-content"
						>

							<div class="content-area__content">
								<back-to-top
									bottom="5%"
									:right="$vs.rtl ? 'calc(100% - 2.2rem - 38px)' : '30px'"
									visibleoffset="500"
									v-if="!hideScrollToTop"
								>
									<vs-button
										icon-pack="feather"
										icon="icon-arrow-up"
										class="shadow-lg btn-back-to-top"
									/>
								</back-to-top>

								<transition
									:name="routerTransition"
									mode="out-in"
									@beforeLeave="beforeLeave"
								>
									<router-view
										:key="contentRouterViewReloader"
										@changeRouteTitle="changeRouteTitle"
										@setAppClasses="(classesStr) => $emit('setAppClasses', classesStr)"
									/>
								</transition>
							</div>
						</div>
					</div>
				</div>
				<the-footer />
			</div>
		</ais-instant-search>
	</div>
</template>


<script>
import BackToTop from "vue-backtotop";
import TheFooter from "@/layouts/components/TheFooter.vue";
import themeConfig from "@/../themeConfigShop.js";

import VNavMenu from "@/layouts/shop/components/vertical-nav-menu/VerticalNavMenu.vue";

import TheNavbarHorizontal from "@/layouts/shop/components/navbar/TheNavbarHorizontal.vue";
import TheNavbarVertical from "@/layouts/shop/components/navbar/TheNavbarVertical.vue";

import { AisInstantSearch } from "vue-instantsearch";
import axios from "@/axios.js";
axios.defaults.withCredentials = true;

export default {
	components: {
		BackToTop,
		TheFooter,
		TheNavbarHorizontal,
		TheNavbarVertical,
		VNavMenu,
		AisInstantSearch,
	},
	data() {
		return {
			searchClient: {
				search(requests) {
					if (window.$route &&
						window.$route.name == 'shop-item-detail-view' &&
						requests
					) {
						var isEmpty = true;
						requests.forEach((req) => {
							if (isEmpty) {
								if (! req ||
									! req.params ||
									! req.params.facetFilters
								) {
									isEmpty = false;
								} else {
									req.params.facetFilters.forEach((filter) => {
										if (isEmpty) {
											if (! filter ||
												(
													filter != "hierarchicalCategories.lvl0:" &&
													filter != "hierarchicalCategories.lvl1:"
												)
											) {
												isEmpty = false;
											}
										}
									});
								}
							}
						});
						if (isEmpty) {
							return Promise.resolve({
								results: requests.map(() => ({
									hits: [],
									nbHits: 0,
									nbPages: 1,
									page: 0,
									processingTimeMS: 0,
								})),
							});
						}
					}
					return axios
						.post(`/search`, requests)
						.then((response) => response.data);
				},
			},
			contentRouterViewReloader: 0,
			disableCustomizer: themeConfig.disableCustomizer,
			disableThemeTour: themeConfig.disableThemeTour,
			dynamicWatchers: {},
			footerType: themeConfig.footerType || "static",
			hideScrollToTop: themeConfig.hideScrollToTop,
			isNavbarDark: false,
			navbarColor: themeConfig.navbarColor || "#fff",
			navbarType: themeConfig.navbarType || "floating",
			menuTitle: themeConfig.menuTitle || "Menu",
			routerTransition: themeConfig.routerTransition || "none",
			routeTitle: this.$route.meta.pageTitle,
			steps: [
				{
					target: "#btnVNavMenuMinToggler",
					content: "Toggle Collapse Sidebar.",
				},
				{
					target: ".vx-navbar__starred-pages",
					content:
						"Create your own bookmarks. You can also re-arrange them using drag & drop.",
				},
				{
					target: ".i18n-locale",
					content: "You can change language from here.",
				},
				{
					target: ".navbar-fuzzy-search",
					content: "Try fuzzy search to visit pages in flash.",
				},
				{
					target: ".customizer-btn",
					content: "Customize template based on your preference",
					params: {
						placement: "left",
					},
				},
				{
					target: ".vs-button.buy-now",
					content: "Buy this awesomeness at affordable price!",
					params: {
						placement: "top",
					},
				},
			],
		};
	},
	watch: {
		$route() {
			this.routeTitle = this.$route.meta.pageTitle;
			window.$route = this.$route;
		},
		isThemeDark(val) {
			const color =
				this.navbarColor == "#fff" && val ? "#10163a" : "#fff";
			this.updateNavbarColor(color);
		},
		"$store.state.mainLayoutType"(val) {
			this.setNavMenuVisibility(val);
			this.disableThemeTour = true;
		},
	},
	computed: {
		navMenuItems() {
			if (
				this.$store &&
				this.$store.state &&
				this.$store.state.shopMenu
			) {
				return this.$store.state.shopMenu;
			}
			return [];
		},
		bodyOverlay() {
			return this.$store.state.bodyOverlay;
		},
		contentAreaClass() {
			if (this.mainLayoutType === "vertical") {
				if (this.verticalNavMenuWidth == "default")
					return "content-area-reduced";
				else if (this.verticalNavMenuWidth == "reduced")
					return "content-area-lg";
				else return "content-area-full";
			}
			// else if(this.mainLayoutType === "boxed") return "content-area-reduced"
			else return "content-area-full";
		},
		footerClasses() {
			return {
				"footer-hidden": this.footerType == "hidden",
				"footer-sticky": this.footerType == "sticky",
				"footer-static": this.footerType == "static",
			};
		},
		isAppPage() {
			return this.$route.meta.no_scroll;
		},
		isThemeDark() {
			return this.$store.state.theme == "dark";
		},
		layoutTypeClass() {
			return `main-${this.mainLayoutType}`;
		},
		mainLayoutType() {
			return this.$store.state.mainLayoutType;
		},
		navbarClasses() {
			return {
				"navbar-hidden": this.navbarType == "hidden",
				"navbar-sticky": this.navbarType == "sticky",
				"navbar-static": this.navbarType == "static",
				"navbar-floating": this.navbarType == "floating",
			};
		},
		verticalNavMenuWidth() {
			return this.$store.state.verticalNavMenuWidth;
		},
		windowWidth() {
			return this.$store.state.windowWidth;
		},
	},
	methods: {
		contentRouterViewReload() {
			this.$nextTick(() => {
				this.contentRouterViewReloader += 1;
			});
		},
		beforeLeave(element) {
			this.$router.isBusy = true;
			this.$store.commit("ROUTER_IS_BUSY", true);
			const Stickedtooltips = document.querySelectorAll(".vs-tooltip");
			if (Stickedtooltips && Stickedtooltips.length) {
				for (const tooltip of Stickedtooltips) {
					tooltip.remove();
				}
			}
			const vsLoading = document.getElementById("content-area");
			if (this && this.$vs && vsLoading) {
				this.$vs.loading({
					color: "#ffffff",
					background: "rgba(0, 0, 0, 0.95)",
					container: "#content-area",
					text: "Carregando...",
				});
			} else {
				const appLoading = document.getElementById("loading-bg");
				if (appLoading) {
					appLoading.style.display = "";
				}
			}
		},
		afterEnter(element) {
			const appLoading = document.getElementById("loading-bg");
			const vsLoading = document.getElementById("content-area");
			if (appLoading) {
				appLoading.style.display = "none";
			}
			if (this && this.$vs && this.$vs.loading && vsLoading) {
				this.$vs.loading.close("#content-area > .con-vs-loading");
			}
			this.$router.isBusy = false;
			this.$store.commit("ROUTER_IS_BUSY", false);
		},
		changeRouteTitle(title) {
			this.routeTitle = title;
		},
		updateNavbar(val) {
			if (val == "static")
				this.updateNavbarColor(this.isThemeDark ? "#10163a" : "#fff");
			this.navbarType = val;
		},
		updateNavbarColor(val) {
			this.navbarColor = val;
			if (val == "#fff") this.isNavbarDark = false;
			else this.isNavbarDark = true;
		},
		updateFooter(val) {
			this.footerType = val;
		},
		updateRouterTransition(val) {
			this.routerTransition = val;
		},
		setNavMenuVisibility(layoutType) {
			if (
				(layoutType === "horizontal" && this.windowWidth >= 1024) ||
				(layoutType === "vertical" && this.windowWidth < 1024)
			) {
				this.$store.commit("TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE", false);
				this.$store.dispatch(
					"updateVerticalNavMenuWidth",
					"no-nav-menu"
				);
			} else {
				this.$store.commit("TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE", true);
			}
		},
		toggleHideScrollToTop(val) {
			this.hideScrollToTop = val;
		},
	},
	created() {
		const color =
			this.navbarColor == "#fff" && this.isThemeDark
				? "#10163a"
				: this.navbarColor;
		this.updateNavbarColor(color);
		this.setNavMenuVisibility(this.$store.state.mainLayoutType);

		// Dynamic Watchers for tour
		// Reason: Once tour is disabled it is not required to enable it.
		// So, watcher is required for just disabling it.
		this.dynamicWatchers.windowWidth = this.$watch(
			"$store.state.windowWidth",
			(val) => {
				if (val < 1024) {
					this.disableThemeTour = true;
					this.dynamicWatchers.windowWidth();
				}
			}
		);

		this.dynamicWatchers.verticalNavMenuWidth = this.$watch(
			"$store.state.verticalNavMenuWidth",
			() => {
				this.disableThemeTour = true;
				this.dynamicWatchers.verticalNavMenuWidth();
			}
		);

		this.dynamicWatchers.rtl = this.$watch("$vs.rtl", () => {
			this.disableThemeTour = true;
			this.dynamicWatchers.rtl();
		});

		//WORKAROUND FOR TEST ONLY
		window.$route = this.$route;
	},
	mounted() {
		window.algoliaSearch = this.$refs.algoliaSearch;
	},
	beforeDestroy() {
		Object.keys(this.dynamicWatchers).map((i) => {
			this.dynamicWatchers[i]();
			delete this.dynamicWatchers[i];
		});
	},
};
</script>

