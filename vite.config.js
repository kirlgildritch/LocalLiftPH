import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/cart.js',
                'resources/js/product-gallery.js',
                'resources/js/purchase-variants.js',
                'resources/js/review-upload.js',
            ],
            refresh: true,
        }),
    ],
});
