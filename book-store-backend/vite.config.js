import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        port: 5173,
        host: '0.0.0.0',
        allowedHosts: ['frontend', 'bookstore.shop', 'localhost', '127.0.0.1'],
        hmr: {
            host: 'bookstore.shop',
            port: 8443,
            protocol: 'wss',
        }
    }
})
