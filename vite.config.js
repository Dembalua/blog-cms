import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme.js', 'resources/css/filament/admin/theme.css', 'resources/js/filament/internal-link-extension.js', 'resources/js/filament/affiliate-link-extension.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        rollupOptions: {
            preserveEntrySignatures: 'exports-only',
        },
    },
});
