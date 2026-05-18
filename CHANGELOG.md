# Changelog

All notable changes to FluxStack will be documented in this file.

Format based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2026-05-19

### Changed
- Complete rebuild on Roots Sage 11 (standalone theme, no longer a Bricks child theme)
- Module system rewritten with OOP architecture (BaseModule, BlockModule, CptModule)
- Build system changed from none to Vite + Tailwind CSS 4
- Block compilation via @wordpress/scripts (webpack)
- Settings pages rebuilt as native WordPress admin (no ACF dependency for settings)
- White label module now uses native color pickers and toggle controls

### Added
- Blade templating engine for all frontend views
- Tailwind CSS 4 with design token system (@theme)
- ModuleServiceProvider for Laravel service container integration
- CptModule base class with auto-discovery of nested blocks
- Site Settings as standalone admin page with sub-page architecture
- Header, Footer, and Home Page settings as toggleable sub-pages
- Modern admin UI with tabbed navigation, AJAX save, toast notifications
- Hero Block (fluxstack/hero) with background, overlay, dual CTAs
- CTA Block (fluxstack/cta) with horizontal/stacked layouts
- File upload fields using WordPress Media Library
- URL input fields with https:// prefix
- Custom checkbox and select styling
- Module Manager shows Site Settings Pages as toggleable group
- Helper functions: site_setting(), theme_option(), get_excerpt(), get_thumbnail_url()
- Documentation in docs/ folder

### Removed
- Bricks Builder dependency
- ACF dependency for theme settings (ACF still optional for CPT fields)
- Utility Functions module (replaced by app/helpers.php)
- Dynamic Snippets module
- Image Gallery module (to be rebuilt as a block)

---

## v1.x (Bricks Builder era)

Previous versions used Bricks Builder as parent theme. That codebase is preserved on the `bricks` branch.

## [1.6.1] - 2025-06-06
### Changed
- Set all content modules to be deactivated by default

## [1.6.0] - 2025-06-05
### Added
- Services module

## [1.5.0] - 2025-06-05
### Added
- Portfolio module

## [1.4.0] - 2025-03-20
### Added
- Module Manager with dependency management
- Block toggle system

## [1.3.0] - 2025-03-20
### Added
- Comprehensive documentation

## [1.2.0] - 2025-03-20
### Added
- Custom button styles for block editor

## [1.1.0] - 2025-03-15
### Added
- Teams, Publications, News Archives modules

## [1.0.0] - 2025-03-10
### Added
- Initial release with Bricks Builder integration
