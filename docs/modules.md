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
- **ACF Fields:** client, year, location, website, project description, features (repeater), key details (repeater), gallery
- **Scaffolded templates:** `archive-portfolio.blade.php`, `single-portfolio.blade.php`
- **Scaffolded CSS:** `resources/css/modules/portfolio.css`
- Project showcase with taxonomy filter, hover overlay cards, meta bar, features grid, image gallery

### Publications
- **ID:** `publications`
- **Post Type:** `publication`
- **Taxonomy:** `publication_type`
- PDF support, date-based organization, admin filtering

## Block Modules

### Hero Block
- **ID:** `hero-block`
- **Block:** `fluxstack/hero`
- **Method:** PHP-only (autoRegister)
- **Default:** Enabled
- Full-width hero with background image URL, overlay opacity, heading, subheading, dual CTAs, text alignment

### CTA Block
- **ID:** `cta-block`
- **Block:** `fluxstack/cta`
- **Method:** PHP-only (autoRegister)
- **Default:** Enabled
- Call-to-action banner with heading, text, button, layout (horizontal/stacked), open-in-new-tab

### Section Wrapper
- **ID:** `section-wrapper`
- **Block:** `fluxstack/section`
- **Method:** PHP-only (autoRegister)
- **Default:** Enabled
- Generic section with heading, subheading, content width, vertical padding size

### Feature Grid
- **ID:** `feature-grid`
- **Block:** `fluxstack/feature-grid`
- **Method:** PHP-only (autoRegister)
- Grid of up to 3 feature cards with dashicon, title, description. Column count selector.

### Icon Box
- **ID:** `icon-box`
- **Block:** `fluxstack/icon-box`
- **Method:** PHP-only (autoRegister)
- Single icon + heading + text. Stacked or horizontal layout.

### Accordion / FAQ
- **ID:** `accordion-block`
- **Block:** `fluxstack/accordion`
- **Method:** JSX (Vite-compiled) + vanilla JS frontend
- **Default:** Enabled
- Dynamic repeater — add/remove/reorder FAQ items in the editor
- Inline `RichText` editing for questions and answers directly in the canvas
- Collapsible items in editor (expand/collapse toggle per item)
- Style options: variant (bordered, cards, minimal), toggle icon (plus/chevron/none), item spacing
- Frontend uses native `<details>` element with vanilla JS lazy-loaded
- Sidebar: Manage Items panel (reorder/delete/edit), Behavior panel, Styles tab

### Stats Counter
- **ID:** `stats-counter`
- **Block:** `fluxstack/stats-counter`
- **Method:** PHP-only (autoRegister) + vanilla JS
- Up to 4 animated number counters with suffix and label. Animates on scroll into view. Column count selector.

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
