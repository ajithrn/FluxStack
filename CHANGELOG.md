# Changelog

All notable changes to the FluxStack theme will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
