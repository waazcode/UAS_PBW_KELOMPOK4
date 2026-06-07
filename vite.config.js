import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/map-peta.js',
                'resources/js/map-pin.js',
            ],
            refresh: true,
        }),
    ],
});
