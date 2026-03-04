# FluxStack Roadmap

## Medium-Term

### Settings Export/Import

Let users export module, white-label, and theme option settings as JSON and import on another FluxStack site. Useful for cloning configurations across client deployments.

### Module Auto-Discovery

Instead of hardcoding modules in `modules.php`, scan the `modules/` directory for a `module.json` manifest file. Adding a new module would only require creating the directory with a manifest — no edits to `modules.php` needed.

### REST API for CPT Modules

Add `show_in_rest => true` to the base CPT class for Gutenberg block editor and headless WordPress support. Currently set to `false`, which limits these post types to the classic editor.

### Performance Module

Lazy-load images, defer scripts, minify inline CSS/JS. Previously discussed but deferred — can be added as a toggleable module.

## Long-Term

### Theme Starter CLI

A WP-CLI command `wp fluxstack init` that scaffolds a new client site:

- Creates `acf-json/custom/` with starter field groups
- Enables relevant modules based on a preset (business, portfolio, news, etc.)
- Sets up white-label branding

### Multi-Site Support

Network-level module management for WordPress multisite installations. Allow enabling/disabling modules at the network level with per-site overrides.

### AI Toolkit Module

GPT-powered SEO content generation for slugs, meta descriptions, keywords, and summaries. Planned in a previous session — would integrate with ACF fields via a settings page for API key and region/language config.
