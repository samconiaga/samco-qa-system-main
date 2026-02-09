import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";

export default defineConfig({
    server: {
        host: "127.0.0.1",
    },
    plugins: [
        laravel({
            input: [
                "resources/sass/app.scss",
                "resources/js/App.jsx",
                "resources/css/print.css",
                "resources/css/kop.css",
            ],
            refresh: true,
        }),

        react(),
    ],
});
