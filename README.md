# FluxStack

A modular WordPress child theme for [Bricks Builder](https://bricksbuilder.io/).

## Overview

FluxStack extends Bricks Builder with a modular architecture for managing custom post types, theme options, SEO, Bricks customizations, and white-label branding. Modules can be individually enabled/disabled via the **Flux Stack Settings** page. Includes GitHub-based auto-updates.

## Requirements

- WordPress 6.0+
- [Bricks Builder](https://bricksbuilder.io/) theme (parent)
- [ACF PRO](https://www.advancedcustomfields.com/pro/) plugin
- PHP 7.4+

## Quick Start

See [INSTALLATION.md](INSTALLATION.md) for detailed setup instructions.

1. Install and activate Bricks Builder (parent theme)
2. Install and activate ACF PRO
3. Upload FluxStack to `wp-content/themes/fluxstack`
4. Activate FluxStack in **Appearance > Themes**
5. Configure modules at **Appearance > FluxStack Settings**

## Theme Structure

```
fluxstack/
├── modules/
│   ├── base/                    # Base abstract classes
│   ├── module-manager/          # Module toggle system + settings UI
│   ├── bricks/                  # Bricks Builder customizations
│   ├── theme-options/           # ACF options pages + GitHub updater
│   │   ├── includes/            # ACF config, GitHub updater classes
│   │   ├── acf-json/            # Base field groups
│   │   ├── acf-json/custom/     # Per-client field group extensions
│   │   └── assets/css/          # Admin styles for options pages
│   ├── seo/                     # SEO meta, OG image, Analytics/GTM
│   ├── utility-functions/       # Helper functions
│   ├── dynamic-snippets/        # Bricks dynamic data snippets
│   ├── white-label/             # Admin branding customization
│   ├── services/                # Services CPT
│   ├── teams/                   # Team Members CPT
│   ├── publications/            # Publications CPT
│   ├── portfolio/               # Portfolio CPT
│   ├── testimonials/            # Testimonials CPT
│   ├── image-gallery/           # Image Gallery CPT
│   └── news-archives/           # Year-based news taxonomy
├── native-blocks/               # Native WordPress blocks
├── .github/workflows/           # GitHub Actions release workflow
├── style.css                    # Theme header (version source)
├── functions.php                # Entry point
├── INSTALLATION.md              # Setup guide
├── DEVELOPMENT.md               # Developer documentation
├── ROADMAP.md                   # Future plans
└── CHANGELOG.md                 # Version history
```

## Settings

- **Appearance > FluxStack Settings** — Module toggles, white-label configuration
- **Theme Options** — Site branding, contact info, social media, footer content (ACF)
- **SEO** (Theme Options > SEO tab) — Meta description, OG image, Google verification, Analytics/GTM

## Auto-Updates

FluxStack checks GitHub releases for updates and shows them in **Dashboard > Updates**.

The repo is configured in `functions.php`:

```php
define('FLUXSTACK_GITHUB_REPO', 'ajithrn/FluxStack');
```

Override in `wp-config.php` to point to a fork if needed.

## Releases

Pushing a version bump in `style.css` to `main` triggers a GitHub Actions workflow that:

1. Reads the version from `style.css`
2. Creates a clean zip (excluding dev files via `.distignore`)
3. Publishes a GitHub release with the zip attached
4. Auto-bumps the patch version if the tag already exists

## Documentation

- [Installation Guide](INSTALLATION.md)
- [Developer Documentation](DEVELOPMENT.md)
- [Changelog](CHANGELOG.md)
- [Roadmap](ROADMAP.md)

## License

This theme is licensed under the GPL v2 or later.
