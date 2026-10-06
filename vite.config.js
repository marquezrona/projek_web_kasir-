import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0', // <-- Tambahkan bagian server ini
        port: 5173,      // (Opsional, pastikan port sesuai)
        hmr: {
            host: process.env.VITE_HMR_HOST || 'localhost',
        },
    },
});