import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: true, // Permite que Vite responda en el dominio virtual de Herd
        hmr: {
            host: 'localhost', // Mantiene la conexión HMR estable
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
