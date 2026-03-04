# FluxStack

A modular WordPress child theme for [Bricks Builder](https://bricksbuilder.io/).

## Overview

FluxStack extends Bricks Builder with a modular architecture for managing custom post types, theme options, Bricks customizations, and white-label branding. Modules can be individually enabled/disabled via the **FluxStack Settings** page.

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
│   ├── theme-options/           # ACF options pages (Header, Footer, Home)
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
├── blocks/                      # Native WordPress blocks
├── style.css                    # Theme header
├── functions.php                # Entry point
├── INSTALLATION.md              # Setup guide
├── DEVELOPMENT.md               # Developer documentation
└── CHANGELOG.md                 # Version history
```

## Settings

- **Appearance > FluxStack Settings** — Module toggles, white-label configuration
- **Theme Options** — Site branding, contact info, social media, header/footer content (ACF)

## Documentation

- [Installation Guide](INSTALLATION.md)
- [Developer Documentation](DEVELOPMENT.md)
- [Changelog](CHANGELOG.md)

## License

This theme is licensed under the GPL v2 or later.
