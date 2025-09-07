import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/soliera.css',
                'resources/css/sidebar-collapse.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
    server: {
        hmr: {
            host: 'localhost',
        },
        cors: true,
        origin: 'http://project.test'
    }
});