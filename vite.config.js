import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
    ],
    // server: {
    //     host: '0.0.0.0',
    //     port: 5176,
    //     strictPort: true,
    //     cors: {
    //         origin: ['http://zero-th.pms.webike.net', 'http://127.0.0.1:8000'],
    //     },
    //     hmr: {
    //         host: 'localhost',
    //     },
    // },
});
