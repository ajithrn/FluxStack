# FluxStack

A modular WordPress starter theme built on Roots Sage 11. Features a toggleable module system, PHP-only Gutenberg blocks, and a modern admin settings interface.

## Quick Start

```bash
composer install
npm install
npm run build
```

Activate the theme in WordPress admin. Requires WordPress 7.0+ for block editor features.

## Requirements

- WordPress 7.0+
- PHP 8.3+
- Node.js 20.19+ or 22.12+
- Composer 2.x
- ACF PRO (optional, for CPT custom fields)

## Documentation

- [Architecture](docs/architecture.md) — Stack, module system, block strategy
- [Development](docs/development.md) — Setup, creating modules and blocks
- [Blocks](docs/blocks.md) — PHP-only and JSX block development guide
- [Deployment](docs/deployment.md) — Production builds and per-project workflow
- [Modules](docs/modules.md) — Available modules and configuration
- [Changelog](CHANGELOG.md) — Version history

## Key Features

- **Modular architecture** — enable/disable features per project via admin UI
- **Hybrid block system:**
  - PHP-only blocks (WP 7.0 `autoRegister`) — no build step, auto-generated editor controls
  - Full JSX blocks (Vite-compiled) — custom editor UI with MediaUpload, InnerBlocks, rich-text
- **Site Settings** — native settings pages replacing ACF dependency for global config
- **Sage 11 foundation** — Blade templates, Tailwind CSS 4, Vite, Laravel service container
- **CPT modules** — self-contained post types with ACF fields, taxonomies, and nested blocks

## References

- [Sage 11 Documentation](https://roots.io/sage/docs/)
- [WordPress Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [PHP-Only Block Registration (WP 7.0)](https://make.wordpress.org/core/2026/03/03/php-only-block-registration/)
- [Tailwind CSS 4](https://tailwindcss.com/docs)

## License

GPL v2 or later.
