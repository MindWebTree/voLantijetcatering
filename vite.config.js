import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'packages/Webkul/Shop/publishable/assets/js/vue-global.js',
                // Velocity
                'packages/Webkul/volantijetcatering/publishable/assets/js/velocity.js',
                'packages/Webkul/volantijetcatering/publishable/assets/css/velocity.css',

                // Shop
                'packages/Webkul/Shop/publishable/assets/js/shop.js',
                'packages/Webkul/Shop/publishable/assets/css/shop.css',

                // Admin
                'packages/Webkul/Admin/publishable/assets/js/admin.js',
                'packages/Webkul/Admin/publishable/assets/css/admin.css',
            ],
            refresh: true,
        }),
    ],

    resolve: {
        alias: {
            vue: 'vue/dist/vue.common.js',
        },
    },
});
