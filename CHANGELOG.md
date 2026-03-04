# Changelog

All notable changes to the FluxStack theme will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.7.0] - 2026-03-04

### Added

- Base CPT module class (`FS_Base_CPT_Module`) for shared custom post type logic
- Tabbed UI for FluxStack Settings page (Modules | White Label)
- White Label settings tab with configurable agency name, URL, platform name, and footer text
- Bricks Builder custom element auto-discovery from `modules/bricks/elements/`
- INSTALLATION.md with setup guide and troubleshooting
- DEVELOPMENT.md with module architecture, creation guide, ACF workflow, and coding standards
- SEO tab in Theme Options (OG image, meta description, Google verification, Analytics/GTM, head scripts)
- Per-client ACF extension point via `acf-json/custom/` directory
- Conditional options sub-pages (Header, Home Page) that auto-register when matching ACF JSON exists
- Modular dynamic snippets architecture with auto-discovery from `snippets/` directory
- Bricks dynamic data snippets for `social_links` and `copyright` (processed output beyond raw ACF fields)
- Favicon field in Theme Options branding

### Changed

- Refactored all 6 content modules (Services, Teams, Publications, Portfolio, Testimonials, Image Gallery) to extend `FS_Base_CPT_Module`
- Rewrote Bricks module to use correct Bricks Builder APIs (`\Bricks\Elements::register_element()`)
- Rewrote White Label module to use `wp_options` storage instead of hardcoded PHP constants
- Consolidated module loading in `modules.php` from two-pass to single-pass pattern
- Simplified README.md to a concise overview with links to detailed docs
- Restructured Theme Options: removed empty Header sub-page, moved site-specific Home Page fields to `acf-json/custom/`
- Refactored dynamic snippets from monolithic file to one-file-per-snippet architecture
- Renamed `get_payroll_button()` to `get_footer_button()`
- Improved ACF field instructions with Bricks dynamic tag hints for clients

### Fixed

- Double initialization bug: all 13 modules were being initialized twice (once in the module file, once in `modules.php`)
- Bricks module was using non-existent filter hooks (`bricks/elements`, `bricks/templates`, `bricks/settings`)

## [1.6.1] - 2025-06-06

### Changed

- Set all content modules to be deactivated by default in module manager
- Core modules (bricks, theme-options, utility-functions, dynamic-snippets, white-label) remain enabled by default

## [1.6.0] - 2025-06-05

### Added

- Services module for service management
- Custom post type for services with featured image support
- ACF field groups for service details (including intro text and content)
- Service features repeater field for highlighting key aspects
- Admin interface with sortable columns and ordering options
- Helper functions for retrieving services
- Documentation for services module in README.md
- Updated main README.md with services module information

## [1.5.0] - 2025-06-05

### Added

- Portfolio module for project showcase management
- Custom post type for portfolio items with portfolio_type taxonomy
- ACF field groups for project details (including client, location, year, website)
- Project features and key details repeater fields
- Portfolio gallery support for project images
- Admin interface with sortable columns for client, year, and type
- Helper functions for retrieving portfolio items by type or year
- Documentation for portfolio module in README.md

## [1.4.0] - 2025-03-20

### Added

- Module Manager for enabling/disabling theme modules
- Admin interface for toggling modules on/off
- Block toggle system for enabling/disabling native blocks
- Automatic dependency management between modules and blocks
- Metadata-driven block dependency system
- Documentation for block toggle system

### Changed

- Improved module loading system with dependency handling
- Enhanced performance by conditionally loading only enabled modules and blocks
- Updated native blocks to support dependency metadata

## [1.3.0] - 2025-03-20

### Added

- Comprehensive documentation with detailed module descriptions
- Separate CHANGELOG.md file for better version tracking

## [1.2.0] - 2025-03-20

### Added

- Custom button styles for the block editor

### Changed

- Improved publications module

## [1.1.3] - 2025-03-18

### Changed

- Implemented modular block system with auto-discovery for native blocks

## [1.1.2] - 2025-03-17

### Changed

- Converted publication_type from meta field to taxonomy for better organization and filtering

## [1.1.1] - 2025-03-16

### Added

- Home Page settings to theme options

## [1.1.0] - 2025-03-15

### Added

- Teams module for team member management
- Publications module for publication management
- News Archives module for year-based news organization

## [1.0.0] - 2025-03-10

### Added

- Initial release of FluxStack WordPress theme
- Basic theme structure
- Bricks Builder integration
- ACF integration
- Dynamic snippets
- Image gallery
- Testimonials
- Theme options
- White Label module
- Utility functions
