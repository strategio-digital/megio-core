import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    resolve: {
        alias: {
            '@/assets': '/assets'
        }
    },
    build: {
        outDir: 'public',
        assetsDir: 'assets',
    },
    plugins: [
        vue(),
        laravel({
            hotFile: 'temp/vite.hot',
            buildDirectory: 'public',
            input: ['assets/main.ts'],
            refresh: ['assets/**', 'view/**']
        }),
    ]
})
