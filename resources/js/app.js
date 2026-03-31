import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import ClickOutside from "./v-click-outside.js";
import "./errorHandler";

createInertiaApp({
    progress: false,
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue");
        return pages[`./Pages/${name}.vue`]();
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) }).use(plugin);
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
