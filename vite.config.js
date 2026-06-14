/**
 * BarPro Studio — Vite Build Pipeline
 *
 * Структура:
 *   src/  → исходники (ES modules)
 *   dist/ → production bundle
 *
 * Команды:
 *   npm run build  → production minified bundle → assets/dist/
 *   npm run dev    → watch + HMR
 *   npm run watch  → build --watch
 */
import { defineConfig } from 'vite';
import { resolve } from 'path';
import { readdirSync, existsSync } from 'fs';

export default defineConfig(({ command }) => {
  const isProd = command === 'build';

  // Определяем точки входа: src/ если есть, иначе прямые файлы
  const hasSrc = existsSync(resolve(__dirname, 'assets/js/src/main.js'));

  const input = hasSrc
    ? {
        main:   resolve(__dirname, 'assets/js/src/main.js'),
        motion: resolve(__dirname, 'assets/js/src/motion.js'),
        styles: resolve(__dirname, 'assets/css/src/main.css'),
      }
    : {
        // Fallback — текущая структура без src/
        main:   resolve(__dirname, 'assets/js/main.js'),
        motion: resolve(__dirname, 'assets/js/motion.js'),
        styles: resolve(__dirname, 'assets/css/studio.css'),
      };

  return {
    root: '.',
    base: '/wp-content/themes/barpro-wp-theme/',

    build: {
      outDir:     'assets/dist',
      emptyOutDir: true,
      minify:     isProd ? 'esbuild' : false,
      sourcemap:  !isProd,

      rollupOptions: {
        input,
        output: {
          entryFileNames: 'js/[name].[hash].min.js',
          chunkFileNames: 'js/chunks/[name].[hash].min.js',
          assetFileNames: (info) => {
            if (info.name?.endsWith('.css')) return 'css/[name].[hash].min.css';
            if (/\.(png|jpg|webp|svg|gif)$/.test(info.name ?? '')) return 'images/[name].[hash][extname]';
            return 'assets/[name].[hash][extname]';
          },
        },
      },

      assetsInlineLimit: 4096,
    },

    css: {
      devSourcemap: true,
    },

    server: {
      port: 5173,
      strictPort: true,
      origin: 'http://localhost:5173',
      // Сообщать WordPress какие файлы изменились
      hmr: { host: 'localhost' },
    },
  };
});
