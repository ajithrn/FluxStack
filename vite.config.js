import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin'
import { wordpressPlugin, wordpressThemeJson } from '@roots/vite-plugin';
import { readdirSync, statSync } from 'node:fs';
import path from 'node:path';

if (! process.env.APP_URL) {
  process.env.APP_URL = 'http://example.test';
}

/**
 * Resolve glob patterns in CSS @import statements.
 * Supports simple wildcards: @import "./modules/*.css"
 */
function cssGlobImport() {
  return {
    name: 'css-glob-import',
    enforce: 'pre',
    transform(code, id) {
      if (!id.endsWith('.css')) return null;

      const globImportRegex = /@import\s+["']([^"']*\*[^"']*)["']\s*;/g;
      let hasGlob = false;
      let result = code;

      result = result.replace(globImportRegex, (match, pattern) => {
        hasGlob = true;
        const dir = path.dirname(id);
        const globDir = path.resolve(dir, path.dirname(pattern));
        const ext = path.extname(pattern) || '.css';

        let files = [];
        try {
          files = readdirSync(globDir)
            .filter(f => f.endsWith(ext) && statSync(path.join(globDir, f)).isFile())
            .map(f => path.join(globDir, f));
        } catch (e) {
          // Directory doesn't exist yet — no files to import
        }

        if (files.length === 0) return '/* no files matched: ' + pattern + ' */';

        return files
          .map(file => `@import "${path.relative(dir, file)}";`)
          .join('\n');
      });

      return hasGlob ? result : null;
    },
  };
}

export default defineConfig({
  base: process.env.VITE_BASE || '/app/themes/fluxstack/public/build/',
  plugins: [
    cssGlobImport(),
    tailwindcss(),
    react({
      include: ['**/*.jsx'],
      jsxRuntime: 'classic',
    }),
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/css/editor.css',
        'resources/js/editor.js',
      ],
      refresh: [
        'resources/views/**/*.blade.php',
        'modules/**/*.blade.php',
        'modules/**/*.php',
      ],
      assets: ['resources/images/**', 'resources/fonts/**'],
    }),

    wordpressPlugin(),

    wordpressThemeJson({
      disableTailwindColors: false,
      disableTailwindFonts: false,
      disableTailwindFontSizes: false,
      disableTailwindBorderRadius: false,
    }),
  ],
  resolve: {
    alias: {
      '@scripts': '/resources/js',
      '@styles': '/resources/css',
      '@fonts': '/resources/fonts',
      '@images': '/resources/images',
      '@modules': '/modules',
    },
  },
  build: {
    rollupOptions: {
      external: [
        '@wordpress/blocks',
        '@wordpress/block-editor',
        '@wordpress/components',
        '@wordpress/element',
        '@wordpress/i18n',
        '@wordpress/dom-ready',
        '@wordpress/data',
        '@wordpress/compose',
        '@wordpress/hooks',
        'react',
        'react/jsx-runtime',
        'react-dom',
      ],
      output: {
        globals: {
          '@wordpress/blocks': 'wp.blocks',
          '@wordpress/block-editor': 'wp.blockEditor',
          '@wordpress/components': 'wp.components',
          '@wordpress/element': 'wp.element',
          '@wordpress/i18n': 'wp.i18n',
          '@wordpress/dom-ready': 'wp.domReady',
          '@wordpress/data': 'wp.data',
          '@wordpress/compose': 'wp.compose',
          '@wordpress/hooks': 'wp.hooks',
          'react': 'React',
          'react/jsx-runtime': 'ReactJSXRuntime',
          'react-dom': 'ReactDOM',
        },
      },
    },
  },
})
