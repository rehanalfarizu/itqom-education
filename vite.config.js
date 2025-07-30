import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';  

export default defineConfig({
  assetsInclude: ['**/*.png'],
  resolve: {
    alias: {
      'vue': 'vue/dist/vue.esm-bundler.js', // ✅ Ini penting
    },
  },
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
    vue(),
  ],
});
