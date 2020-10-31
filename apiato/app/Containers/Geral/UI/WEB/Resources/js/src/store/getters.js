import themeConfig from "@/../themeConfig.js";

const getters = {

	// COMPONENT
	// vx-autosuggest
	// starredPages: state => state.navbarSearchAndPinList.data.filter((page) => page.highlightAction),
	windowBreakPoint: state => {

		// This should be same as tailwind. So, it stays in sync with tailwind utility classes
		if (state.windowWidth >= 1200) return "xl";
		else if (state.windowWidth >= 992) return "lg";
		else if (state.windowWidth >= 768) return "md";
		else if (state.windowWidth >= 576) return "sm";
		else return "xs";
	},
	isUserLoggedIn: state => {
		if (state.AppActiveUser &&
			state.AppActiveUser.uid &&
			state.AppActiveUser.token &&
			state.AppActiveUser.userRole &&
			state.AppActiveUser.displayName &&
			localStorage.getItem(themeConfig.userInfoLocalStorageKey)
		) {
			return true;
		}
		return false;
	}
};

export default getters;
