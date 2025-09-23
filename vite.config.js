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
    build: {
        // Optymalizacje dla produkcji
        rollupOptions: {
            output: {
                // Code splitting dla lepszej wydajności
                manualChunks: (id) => {
                    // Grupuj node_modules w vendor chunk
                    if (id.includes('node_modules')) {
                        return 'vendor';
                    }
                    // Grupuj Filament w osobny chunk
                    if (id.includes('@filament') || id.includes('filament')) {
                        return 'filament';
                    }
                },
                // Optymalizacja nazw plików
                chunkFileNames: 'assets/js/[name]-[hash].js',
                entryFileNames: 'assets/js/[name]-[hash].js',
                assetFileNames: 'assets/[ext]/[name]-[hash].[ext]',
            },
        },
        // Optymalizacja rozmiaru
        minify: 'esbuild', // Użyj esbuild zamiast terser
        esbuild: {
            drop: ['console', 'debugger'], // Usuń console.log w produkcji
        },
        // Optymalizacja CSS
        cssCodeSplit: true,
        // Optymalizacja chunków
        chunkSizeWarningLimit: 1000,
    },
    // Optymalizacje dla development
    server: {
        hmr: {
            overlay: false, // Wyłącz overlay błędów HMR
        },
    },
    // Optymalizacja zależności
    optimizeDeps: {
        include: ['alpinejs'],
    },
});
