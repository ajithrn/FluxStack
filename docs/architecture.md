# Architecture

## Overview

FluxStack v2 is a standalone WordPress theme built on Roots Sage 11. It uses a modular architecture where features, blocks, and CPTs are self-contained modules that can be toggled on/off per project.

## Stack

| Layer | Technology |
|-------|-----------|
| Theme Framework | Sage 11 (Acorn 6) |
| Templates | Blade |
| Build | Vite (single command) |
| CSS | Tailwind CSS 4 |
| Blocks (simple) | PHP-only `autoRegister` (WP 7.0+) |
| Blocks (complex) | JSX via Vite + @vitejs/plugin-react |
| Container | Laravel Service Container (via Acorn) |

## Sage 11 Foundation

FluxStack is built on [Roots Sage](https://roots.io/sage/) — a WordPress starter theme with modern tooling.

**What Sage provides:**

- **Blade templating** — Laravel's template engine for views
- **Acorn** — Laravel service container, config, and view composers in WordPress
- **Vite** — Fast dev server with HMR, optimized production builds
- **Tailwind CSS** — Utility-first CSS with auto-generated `theme.json` for the block editor
- **PSR-4 autoloading** — Namespaced PHP via Composer
- **View Composers** — Attach data to Blade views cleanly

**Key Sage conventions:**

- `app/setup.php` — theme support, menus, sidebars, asset enqueueing
- `resources/js/editor.js` — block editor scripts (styles, variants, JSX block imports)
- `resources/css/editor.css` — block editor styles
- `theme.json` — auto-generated from Tailwind config on build (don't edit manually)
- `public/` — compiled output (gitignored, never edit)

**Resources:**

- [Sage Documentation](https://roots.io/sage/docs/)
- [Sage GitHub](https://github.com/roots/sage)
- [Acorn Documentation](https://roots.io/acorn/docs/)
- [Roots Community](https://discourse.roots.io/)

## Directory Structure

```
fluxstack/
├── app/
│   ├── Modules/                # Module system core
│   │   ├── BaseModule.php      # Abstract base for all modules
│   │   ├── BlockModule.php     # Base for JSX block modules
│   │   ├── CptModule.php       # Base for CPT modules
│   │   ├── ModuleManager.php   # Discovery, toggle, boot
│   │   └── BlockRenderer.php   # Blade-compatible block rendering
│   ├── Providers/
│   │   ├── ThemeServiceProvider.php
│   │   └── ModuleServiceProvider.php
│   ├── View/Composers/        # Blade view composers
│   ├── helpers.php            # Global helpers (site_setting, get_excerpt, etc.)
│   ├── setup.php              # Theme support, menus, sidebars
│   └── filters.php            # WordPress filters
├── config/
│   └── modules.php            # Core modules list
├── modules/                   # All feature modules
│   ├── module-manager/        # Admin UI for toggling modules
│   ├── site-settings/         # Site settings with sub-pages
│   │   ├── general/           # Branding, contact, social, analytics
│   │   ├── header/            # Header layout and options
│   │   ├── footer/            # Footer layout and options
│   │   └── home/              # Home page sections
│   ├── white-label/           # Admin branding and cleanup
│   ├── seo/                   # Meta tags, Open Graph, schema
│   ├── hero-block/            # Hero section (PHP-only block)
│   ├── cta-block/             # CTA banner (PHP-only block)
│   ├── testimonials/          # Testimonials CPT + ACF fields
│   ├── teams/                 # Team members CPT
│   ├── services/              # Services CPT
│   ├── portfolio/             # Portfolio CPT
│   ├── publications/          # Publications CPT
│   └── news-archives/         # Year-based post taxonomy
├── resources/
│   ├── views/                 # Blade templates (WP template hierarchy)
│   ├── css/                   # Tailwind source (app.css, editor.css)
│   ├── js/                    # Scripts (app.js, editor.js)
│   ├── fonts/
│   └── images/
├── public/build/              # Compiled assets (gitignored)
├── docs/                      # Documentation
├── functions.php              # Entry point (Acorn boot + module fallback)
├── style.css                  # Theme header metadata
├── theme.json                 # Auto-generated from Tailwind (don't edit)
└── vite.config.js             # Build configuration
```

## Block Strategy (Hybrid)

FluxStack uses two approaches for blocks, chosen based on complexity:

### PHP-Only Blocks (primary method, WP 7.0+)

For blocks with simple controls (text, select, toggle, number). No JavaScript, no build step.

| What you get | How |
|---|---|
| Auto-generated sidebar controls | WordPress reads your `attributes` array |
| Server-side render in editor | Via `ServerSideRender` + REST API |
| Native color/spacing/typography panels | Via `supports` array |
| Frontend render | Your `render_callback` PHP function |

**Files needed:** `module.php` + `style.css`

**When to use:** CTA banners, notices, headings, cards, counters, author boxes — anything with text/select/toggle/number inputs only.

**Reference:** [WP Core Trac #64639](https://core.trac.wordpress.org/ticket/64639), [Dev Note](https://make.wordpress.org/core/2026/03/03/php-only-block-registration/)

### JSX Blocks (for complex editor UI)

For blocks needing MediaUpload, InnerBlocks, drag-drop, or custom React components.

**Files needed:** `module.php` + `editor.jsx` + `render.php` + `style.css`

The `editor.jsx` is imported in `resources/js/editor.js` and compiled by Vite with `@vitejs/plugin-react`. WordPress packages (`@wordpress/blocks`, etc.) are externalized — they load from WP globals, not bundled.

**When to use:** Image galleries, hero with media picker, blocks with nested content, sliders.

### Decision Rule

> If a block only needs TextControl, SelectControl, ToggleControl, or NumberControl → use PHP-only.
> If it needs MediaUpload, InnerBlocks, RichText inline editing, or custom React → use JSX.

### Limitations of PHP-Only Blocks

| Limitation | Notes |
|---|---|
| No InnerBlocks | Use JSX for nested content |
| No media/file upload controls | Use JSX or accept URL as text field |
| No inline rich-text editing | Editor shows form controls, not WYSIWYG |
| Preview not live-reactive | Changes trigger server round-trip |
| Reserved attribute names | Never use: `style`, `className`, `textColor`, `backgroundColor`, `fontSize`, `fontFamily`, `align`, `anchor` |

### Editor Placeholder Previews

PHP-only blocks show a styled placeholder with sample content when first inserted (attributes are empty). This helps users understand what the block will look like and directs them to the sidebar panel for editing.

- Placeholders only appear in the editor (detected via `REST_REQUEST`)
- They disappear automatically once the user fills in any content
- Each block shows a contextual sample (e.g. stats show sample numbers, CTA shows a sample layout)
- The shared placeholder CSS is in `resources/css/editor-placeholder.css`
- Blocks use `$this->renderPlaceholder()` from `BaseModule` — no per-block CSS needed

## Module System

### How It Works

1. `functions.php` boots Acorn and registers `ModuleServiceProvider`
2. Fallback in `functions.php` ensures modules load even without Acorn
3. `ModuleManager::discover()` scans `modules/` for `module.php` files (skips `_` prefixed dirs)
4. Each module returns an anonymous class extending `BaseModule`
5. Enabled modules have `register()` called, then `boot()`
6. Module state stored in `wp_options` as `fluxstack_modules`
7. Site settings stored in `wp_options` as `fluxstack_site_settings`

### Module Types

| Type | Base Class | Purpose |
|------|-----------|---------|
| Feature | `BaseModule` | General functionality |
| CPT | `CptModule` | Custom post types + taxonomies + ACF fields |
| Block (PHP) | `BaseModule` | PHP-only registered blocks |
| Block (JSX) | `BlockModule` | Blocks needing JSX editor UI |

## Site Settings Architecture

```
Site Settings (top-level admin menu)
├── General (core, always on)
├── Home Page (toggleable via Module Manager)
├── Header (toggleable)
└── Footer (toggleable)
```

- Sub-pages auto-discovered from `modules/site-settings/*/module.php`
- Each returns a config array with `id`, `title`, `slug`, `priority`, `callback`
- All save to single `fluxstack_site_settings` option with merge behavior
- Modules can register additional sub-pages via `fluxstack_register_settings_pages` action

## Build Pipeline

**Single command:** `npm run build`

1. Vite processes `resources/css/` and `resources/js/`
2. `@roots/vite-plugin` generates `theme.json` from Tailwind config
3. `@vitejs/plugin-react` handles `.jsx` files (if any are imported in `editor.js`)
4. Output goes to `public/build/`
5. PHP-only blocks need no compilation at all

## Key Differences from v1

| v1 (Bricks) | v2 (Sage) |
|---|---|
| Bricks child theme | Standalone Sage 11 |
| Bricks elements | Native Gutenberg blocks (PHP-only + JSX) |
| Bricks templates | Blade templates |
| No build system | Vite (single command) |
| ACF for all settings | Native settings pages (ACF optional for CPT fields) |
| Procedural module loader | OOP with service container |
| wp-scripts for blocks | PHP-only for simple, Vite for complex |
