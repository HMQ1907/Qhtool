import { router } from "@inertiajs/vue3";
import { reactive } from "vue";

export function checkActiveMenu(item, route) {
    if (!route || !item) return false;

    if (item.children && item.children.length > 0) {
        return (
            (item.name && isRouteActive(item.name, route)) ||
            item.children.some(child => checkActiveMenu(child, route))
        );
    }

    return item.name ? isRouteActive(item.name, route) : false;
}

function isRouteActive(name, route) {
    if (route.current && typeof route.current === "function") {
        return route.current(name);
    }
    if (route.name) {
        return route.name === name;
    }
    return false;
}

export const resetRoute = (routeName, params = {}) => {
    router.get(route(routeName), params, {
        preserveState: false,
        onError: (error) => {
            window.notyf.error("A server error occurred. Please contact the dev team to support you.");
        },
    });
};

export const state = reactive({
    isHiddenMenu: false,
});

export const toggleHiddenMenu = () => {
    state.isHiddenMenu = !state.isHiddenMenu;
};

export const getImageUrl = (imagePath) => {
    if (!imagePath) {
        return "/images/no_img.jpg";
    }
    return imagePath;
};
