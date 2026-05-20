# FluxStack

A modular WordPress starter theme built on Roots Sage 11. Features a toggleable module system, hybrid Gutenberg blocks, modular CSS architecture, and self-scaffolding CPT modules.

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

- [Architecture](docs/architecture.md) — Stack, module system, block strategy, CSS architecture
- [Development](docs/development.md) — Setup, creating modules, blocks, and templates
- [Blocks](docs/blocks.md) — PHP-only and JSX block development guide
- [Deployment](docs/deployment.md) — Production builds and per-project workflow
- [Modules](docs/modules.md) — Available modules and configuration
- [Changelog](CHANGELOG.md) — Version history

## Key Features

- **Modular architecture** — enable/disable features per project via admin UI
- **Hybrid block system:**
  - PHP-only blocks (WP 7.0 `autoRegister`) — no build step, auto-generated editor controls
  - Full JSX blocks (Vite-compiled) — custom editor UI with repeaters, InnerBlocks, RichText
- **Modular CSS** — split into config, sections, components, and module styles
- **Design tokens** — single `config.css` file for per-project colors, fonts, sizing
- **Self-scaffolding CPT modules** — templates and CSS auto-copy to theme on first activation
- **Plugin compatibility** — `header.php`/`footer.php` bridge for plugins using `get_header()`
- **Site Settings** — native settings pages replacing ACF dependency for global config
- **Sage 11 foundation** — Blade templates, Tailwind CSS 4, Vite, Laravel service container

## Per-Project Customization

```
resources/css/config.css       ← Change colors, fonts, sizing here
resources/css/sections/        ← Customize layout sections
resources/css/modules/         ← Customize module-specific styles
resources/views/               ← Customize templates (CPT views scaffolded here)
```

Module skeletons (views + CSS) are copied on first activation and never overwritten. Edit the copies in `resources/` for per-project changes.

## References

- [Sage 11 Documentation](https://roots.io/sage/docs/)
- [WordPress Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [PHP-Only Block Registration (WP 7.0)](https://make.wordpress.org/core/2026/03/03/php-only-block-registration/)
- [Tailwind CSS 4](https://tailwindcss.com/docs)

## License

GPL v2 or later.
