import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin'
import { wordpressPlugin, wordpressThemeJson } from '@roots/vite-plugin';

if (! process.env.APP_URL) {
  process.env.APP_URL = 'http://example.test';
}

export default defineConfig({
  base: '/app/themes/fluxstack/public/build/',
  plugins: [
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
