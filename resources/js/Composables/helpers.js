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

export const getProductVariant = (row) => {
    const productData = row.product.data;
    if (productData.variants && productData.variants.length > 0) {
        return productData.variants
            .map((variant) => {
                const key = Object.keys(variant)[0];
                const value = variant[key];
                return key ? value : "";
            })
            .join("\n");
    }
    return "";
};

export const getCurrencyIcon = (currency) => {
    switch (currency) {
        case "IDR":
            return "fa-rupiah-sign";
        case "EUR":
            return "fa-euro-sign";
        case "THB":
            return "fa-baht-sign";
        case "JPY":
            return "fa-yen-sign";
     default:
        return "fa-dollar-sign";
    }
};
