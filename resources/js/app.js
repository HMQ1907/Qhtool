import { createApp, h, ref } from "vue";
import { createInertiaApp, router } from "@inertiajs/vue3";
import ClickOutside from "./v-click-outside.js";
import "./errorHandler";
import InertiaLoadingOverlay from "./Components/InertiaLoadingOverlay.vue";

const isLoading = ref(false);

router.on('start', () => {
    isLoading.value = true;
});

router.on('finish', () => {
    isLoading.value = false;
});

router.on('error', () => {
    isLoading.value = false;
});

router.on('navigate', () => {
    isLoading.value = false;
});

createInertiaApp({
    progress: false,
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue");
        return pages[`./Pages/${name}.vue`]();
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({
            render: () =>
                h('div', [
                    h(App, props),
                    h(InertiaLoadingOverlay, { isLoading: isLoading.value }),
                ]),
        }).use(plugin);
        app.config.globalProperties.$route = route;
        app.config.globalProperties.$filters = {
            formatNumber(number) {
                return number != undefined
                    ? Intl.NumberFormat().format(number)
                    : "";
            },
        };
        app.directive("click-outside", ClickOutside);
        app.mount(el);
    },
});
