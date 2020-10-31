import navbarSearchAndPinList from "@/layouts/components/navbar/navbarSearchAndPinList";
import themeConfig from "@/../themeConfigShop.js";
import colors from "@/../themeConfigShop.js";

// /////////////////////////////////////////////
// State
// /////////////////////////////////////////////

const state = {
	bodyOverlay: false,
	isVerticalNavMenuActive: true,
	mainLayoutType: themeConfig.mainLayoutType || "vertical",
	navbarSearchAndPinList: navbarSearchAndPinList,
	reduceButton: themeConfig.sidebarCollapsed,
	verticalNavMenuWidth: "default",
	verticalNavMenuItemsMin: false,
	scrollY: 0,
	starredPages: navbarSearchAndPinList["pages"].data.filter((page) => page.is_bookmarked),
	theme: themeConfig.theme || "light",
	themePrimaryColor: colors.primary,
	shopMenu: [],
	// Can be used to get current window with
	// Note: Above breakpoint state is for internal use of sidebar & navbar component
	windowWidth: null,
	routerIsBusy: false,
};

export default state;
