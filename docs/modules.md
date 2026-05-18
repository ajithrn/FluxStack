# Modules

## Core Modules (always enabled)

### Module Manager
- **ID:** `module-manager`
- **Location:** Appearance > FluxStack
- Admin UI for toggling modules and blocks on/off

### Site Settings
- **ID:** `site-settings`
- **Location:** Site Settings (top-level menu)
- Global settings with extensible sub-pages

## Feature Modules

### White Label
- **ID:** `white-label`
- **Default:** Enabled
- Admin branding, login page customization, color scheme, dashboard cleanup
- Settings tab in Module Manager for agency name, colors, visibility toggles

### News Archives
- **ID:** `news-archives`
- **Default:** Disabled
- Adds a `news_year` taxonomy to posts
- Auto-assigns posts to year terms on publish
- Admin column and filter for year-based browsing

## Content Type Modules

### Testimonials
- **ID:** `testimonials`
- **Post Type:** `testimonial`
- **Taxonomy:** `testimonial_category`
- Rating system, admin columns, sortable by rating

### Teams
- **ID:** `teams`
- **Post Type:** `team_member`
- **Taxonomy:** `team_category`
- Team member profiles with designation field

### Services
- **ID:** `services`
- **Post Type:** `service`
- Orderable services with thumbnails

### Portfolio
- **ID:** `portfolio`
- **Post Type:** `portfolio`
- **Taxonomy:** `portfolio_type`
- Project showcase with client, year, and type metadata

### Publications
- **ID:** `publications`
- **Post Type:** `publication`
- **Taxonomy:** `publication_type`
- PDF support, date-based organization, admin filtering

## Block Modules

### Hero Block
- **ID:** `hero-block`
- **Block:** `fluxstack/hero`
- **Default:** Enabled
- Full-width hero with background image, overlay, heading, subheading, dual CTAs

### CTA Block
- **ID:** `cta-block`
- **Block:** `fluxstack/cta`
- **Default:** Enabled
- Call-to-action banner with horizontal or stacked layout

## Site Settings Sub-Pages

These appear under the Site Settings menu when enabled:

### Home Page
- **ID:** `site-settings-home`
- **Priority:** 10
- Hero section config, featured sections toggles

### Header
- **ID:** `site-settings-header`
- **Priority:** 20
- Layout, top bar, CTA button, mobile menu style

### Footer
- **ID:** `site-settings-footer`
- **Priority:** 30
- Column layout, colors, content, bottom bar

## Adding Project-Specific Modules

For per-project features, create a new module in `modules/`:

```
modules/my-project-feature/
└── module.php
```

The module will be auto-discovered and appear in the Module Manager.

For site settings sub-pages, add a folder inside `modules/site-settings/`:

```
modules/site-settings/my-page/
├── module.php      # Config array with id, title, slug, priority
└── views/page.php  # Settings page view
```
