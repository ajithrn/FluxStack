# Architecture

## Overview

FluxStack v2 is a standalone WordPress theme built on Roots Sage 11. It replaces the previous Bricks Builder child theme with a native block editor approach while keeping the same modular architecture.

## Stack

| Layer | Technology |
|-------|-----------|
| Framework | Sage 11 (Acorn 6) |
| Templates | Blade |
| Build | Vite |
| CSS | Tailwind CSS 4 |
| Blocks | WordPress Block API + wp-scripts |
| Container | Laravel Service Container (via Acorn) |

## Sage 11 Foundation

FluxStack is built on [Roots Sage](https://roots.io/sage/) — a WordPress starter theme that brings modern development tooling to WordPress.

**Key features Sage provides:**

- **Blade templating** — Laravel's templating engine for clean, reusable views
- **Acorn** — Integrates Laravel's service container, config system, and view composers into WordPress
- **Vite** — Fast dev server with HMR, optimized production builds
- **Tailwind CSS** — Utility-first CSS with automatic theme.json generation for the block editor
- **PSR-4 autoloading** — Namespaced PHP classes via Composer
- **View Composers** — Attach data to Blade views without polluting templates

**Resources:**

- [Sage Documentation](https://roots.io/sage/docs/)
- [Sage GitHub Repository](https://github.com/roots/sage)
- [Acorn Documentation](https://roots.io/acorn/docs/)
- [Roots Discourse (Community)](https://discourse.roots.io/)

## Directory Structure

```
fluxstack/
├── app/
│   ├── Modules/                # Module system core
│   │   ├── BaseModule.php      # Abstract base for all modules
│   │   ├── BlockModule.php     # Base for block modules
│   │   ├── CptModule.php       # Base for CPT modules
│   │   ├── ModuleManager.php   # Discovery, toggle, boot
│   │   └── BlockRenderer.php   # Blade-compatible block rendering
│   ├── Providers/
│   │   ├── ThemeServiceProvider.php
│   │   └── ModuleServiceProvider.php
│   ├── View/Composers/        # Blade view composers
│   ├── helpers.php            # Global helper functions
│   ├── setup.php              # Theme support, menus, sidebars
│   └── filters.php            # WordPress filters
├── config/
│   └── modules.php            # Core modules, defaults
├── modules/                   # All feature modules
│   ├── module-manager/        # Admin UI for toggling modules
│   ├── site-settings/         # Site settings with sub-pages
│   │   ├── general/           # Branding, contact, social, analytics
│   │   ├── header/            # Header layout and options
│   │   ├── footer/            # Footer layout and options
│   │   └── home/              # Home page sections
│   ├── white-label/           # Admin branding and cleanup
│   ├── hero-block/            # Hero section block
│   ├── cta-block/             # CTA banner block
│   ├── testimonials/          # Testimonials CPT
│   ├── teams/                 # Team members CPT
│   ├── services/              # Services CPT
│   ├── portfolio/             # Portfolio CPT
│   ├── publications/          # Publications CPT
│   └── news-archives/         # Year-based post taxonomy
├── resources/
│   ├── views/                 # Blade templates
│   ├── css/                   # Tailwind source
│   ├── js/                    # App + editor scripts
│   ├── fonts/
│   └── images/
├── public/                    # Compiled assets (gitignored)
│   ├── build/                 # Vite output
│   └── blocks/                # wp-scripts block output
├── functions.php              # Entry point
├── style.css                  # Theme header
├── theme.json                 # Block editor config
├── vite.config.js             # Vite build config
└── webpack.blocks.config.cjs  # Block compilation config
```

## Module System

### How It Works

1. `functions.php` boots Acorn and registers the `ModuleServiceProvider`
2. A fallback in `functions.php` ensures modules load even without Acorn
3. `ModuleManager::discover()` scans `modules/` for `module.php` files
4. Each module returns a class extending `BaseModule`, `BlockModule`, or `CptModule`
5. Enabled modules have `register()` called, then `boot()`
6. Module state is stored in `wp_options` as `fluxstack_modules`

### Module Types

| Type | Base Class | Purpose |
|------|-----------|---------|
| Feature | `BaseModule` | General functionality (white-label, news-archives) |
| CPT | `CptModule` | Custom post types with taxonomies |
| Block | `BlockModule` | Gutenberg blocks |

### Creating a Module

```php
<?php
use App\Modules\BaseModule;

return new class extends BaseModule {
    public function id(): string { return 'my-module'; }
    public function name(): string { return 'My Module'; }
    public function description(): string { return 'What it does.'; }
    public function category(): string { return 'feature'; }
    public function register(): void { /* hooks, filters */ }
};
```

## Site Settings Architecture

Site Settings uses a sub-page pattern:

- `site-settings/module.php` — Core module, registers parent menu
- `site-settings/general/` — Always-on General page (branding, contact, social, analytics)
- `site-settings/header/` — Toggleable Header settings
- `site-settings/footer/` — Toggleable Footer settings
- `site-settings/home/` — Toggleable Home Page settings

Sub-pages return a config array and are auto-discovered. All save to a single `fluxstack_site_settings` option with merge behavior.

## Block Architecture

Standalone blocks live in `modules/` as top-level modules. CPT-related blocks live inside their parent module's `blocks/` subdirectory.

Block compilation uses `@wordpress/scripts` (webpack) outputting to `public/blocks/`. Source `editor.js` files stay in the module folder.

## Key Differences from v1

| v1 (Bricks) | v2 (Sage) |
|-------------|-----------|
| Bricks child theme | Standalone Sage 11 |
| Bricks elements | Native Gutenberg blocks |
| Bricks templates | Blade templates |
| No build system | Vite + wp-scripts |
| ACF for all settings | Native settings pages + optional ACF for CPT fields |
| Procedural module loader | OOP with service container |
| Static class methods | Anonymous class instances |
