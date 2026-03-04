# Installation Guide

## Prerequisites

Before installing FluxStack, ensure you have:

1. **WordPress 6.0+** installed and running
2. **Bricks Builder** theme purchased, installed, and activated
3. **ACF PRO** plugin installed and activated (required for theme options and custom fields)
4. **PHP 7.4+** on your server

## Installation

### 1. Install Parent Theme

Install and activate [Bricks Builder](https://bricksbuilder.io/) as your WordPress theme.

### 2. Install ACF PRO

1. Download ACF PRO from your [account](https://www.advancedcustomfields.com/my-account/)
2. Go to **Plugins > Add New > Upload Plugin**
3. Upload and activate ACF PRO

### 3. Install FluxStack

**Option A: Git Clone (recommended for development)**

```bash
cd wp-content/themes/
git clone git@github.com:ajithrn/FluxStack.git fluxstack
```

**Option B: Upload via WordPress Admin**

1. Download/zip the FluxStack theme folder
2. Go to **Appearance > Themes > Add New > Upload Theme**
3. Upload the zip file and activate

### 4. Activate FluxStack

Go to **Appearance > Themes** and activate **FluxStack**.

## Post-Installation

### Configure Modules

1. Go to **Appearance > FluxStack Settings**
2. Enable/disable modules based on your site requirements
3. Core modules (Bricks, Utility Functions, Theme Options, Dynamic Snippets, White Label) are enabled by default
4. Content modules (Services, Teams, Publications, Portfolio, Testimonials, Image Gallery) are disabled by default

### Configure Theme Options

1. Go to **Theme Options** in the admin sidebar
2. Set up **General Settings** (logo, contact info, social links)
3. Configure **Header Settings**, **Footer Settings**, and **Home Page Settings** as needed

### Configure White Label (Optional)

1. Go to **Appearance > FluxStack Settings > White Label** tab
2. Set your agency name, URL, platform name, and footer text
3. These settings customize the WordPress admin branding

### ACF Field Groups

ACF field groups are automatically loaded from each module's `acf-json/` directory. No manual import is needed — they sync automatically when you visit the **Custom Fields** admin page.

## Updating

To update the theme:

```bash
cd wp-content/themes/fluxstack
git pull origin main
```

If using the WordPress admin, replace the theme folder with the updated version and re-activate.

## Troubleshooting

### Theme Activation Errors

- Ensure Bricks Builder (parent theme) is installed and active
- Ensure ACF PRO is installed and active
- Check PHP version is 7.4 or higher

### Missing Custom Fields

- Visit **Custom Fields** in the admin to trigger ACF JSON sync
- Ensure the relevant module is enabled in **FluxStack Settings**

### Module Not Working

- Check **Appearance > FluxStack Settings** to confirm the module is enabled
- Some modules have dependencies (e.g., blocks may require specific modules)
- Enable `WP_DEBUG` and check `wp-content/debug.log` for errors
