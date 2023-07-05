import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    resolve: {
        alias: {
            '@/assets': '/assets',
            '@/saas': '/vendor/strategio/saas/vue',
            './vue': '/vendor/strategio/saas/vue',
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
            input: ['assets/app.ts', 'assets/saas.ts'],
            refresh: ['assets/**', 'view/**']
        }),
    ]
})
