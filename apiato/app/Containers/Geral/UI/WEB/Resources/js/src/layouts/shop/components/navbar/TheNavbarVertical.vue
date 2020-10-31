<template>
	<div class="relative">
		<div
			class="vx-navbar-wrapper vx-navbar-wrapper-vertical"
			:class="classObj"
		>
			<vs-navbar
				class="vx-navbar navbar-custom navbar-skelton text-dark"
			>
				<feather-icon
					class="sm:inline-flex xl:hidden cursor-pointer p-2"
					icon="MenuIcon"
					@click.stop="showSidebar"
				/>
				<vs-spacer />
				<div class="vx-logo flex items-center logoMobile-wrapper">
					<logo class="logoMobile" :onClick="goToIndex" />
				</div>
				<ais-search-box ref="menuSearch">
					<div slot-scope="{ currentRefinement, isSearchStalled, refine }">
						<div class="relative">
							<vs-input
								class="w-full vs-input-shadow-drop vs-input-no-border d-theme-input-dark-bg hidden"
								:class="{
							'showSearch': showFullSearch
						}"
								v-model="currentRefinement"
								ref="searchInput"
								@input="refine($event)"
								@keyup.esc="refine('');showFullSearch=false;"
								@focus="showFullSearch = true"
								@blur="showFullSearch = false"
								size="large"
							/>
							<div
								slot="submit-icon"
								class="cursor-pointer"
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
								class="cursor-pointer"
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

				<cart-drop-down />

			</vs-navbar>
		</div>
	</div>
</template>


<script>
import CartDropDown from "./components/CartDropDown.vue";
import Logo from "../Logo.vue";
import { AisSearchBox } from "vue-instantsearch";

export default {
	name: "the-navbar-vertical",
	props: {
		navbarColor: {
			type: String,
			default: "#fff",
		},
	},
	components: {
		CartDropDown,
		AisSearchBox,
		Logo,
	},
	data() {
		return {
			showFullSearch: false,
		};
	},
	computed: {
		verticalNavMenuWidth() {
			return this.$store.state.verticalNavMenuWidth;
		},
		windowWidth() {
			return this.$store.state.windowWidth;
		},
		classObj() {
			if (this.verticalNavMenuWidth == "default") return "navbar-default";
			else if (this.verticalNavMenuWidth == "reduced")
				return "navbar-reduced";
			else if (this.verticalNavMenuWidth) return "navbar-full";
		},
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
		showSidebar() {
			this.$store.commit("TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE", true);
		},
	},
	mounted() {
		window.menuSearch = this.$refs.menuSearch;
	}
};
</script>
<style lang="scss">
.vx-navbar-wrapper-vertical {
	background-color: #F9F9F9;
	header.vx-navbar {
		background: #F9F9F9 !important;
	}
	div.ais-SearchBox div.vs-component.showSearch {
		display: block !important;
		position: fixed;
		top: 0px;
		left: 0px;
		right: 0px;
		z-index: 10;
	}
	div.logoMobile-wrapper {
		margin: auto;
		width: 100%;
		justify-content: center;
		img.logoMobile {
			height: 26px;
		}
	}
}
</style>
