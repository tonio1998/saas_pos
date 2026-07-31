import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    server: {
        host: 'localhost',
        cors: true,
        hmr: {
            host: 'saaskit.dev.com'
        }
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/pages/terminal.js',
                'resources/js/pages/pos/product-suggestions.js'
            ],
            refresh: true,
        }),
    ],
})
