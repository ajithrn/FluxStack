# FluxStack v2 - Implementation Tasks

**Theme:** FluxStack v2 (Sage 11 + Vite + Tailwind 4)  
**Replaces:** FluxStack v1 (Bricks child theme → moves to `bricks-legacy` branch)  
**Last Updated:** 2026-05-18

---

## Revisions from Original Plan

| Original Plan | Actual Implementation |
|---|---|
| Sage 10 | Sage 11 (v11.2.1) |
| Bud (Webpack) build system | Vite |
| SCSS for styles | Tailwind CSS 4 + vanilla CSS |
| `resources/scripts/` & `resources/styles/` | `resources/js/` & `resources/css/` |
| `bud.config.js` | `vite.config.js` |
| `public/` compiled output | `public/` compiled output (same) |
| PHP 8.1+ | PHP 8.3+ (Sage 11 requirement) |
| Node 18+ | Node 20.19+ or 22.12+ |

---

## Phase 1: Foundation (v1.0)

### 1.1 Module System Core
- [x] Create `app/Modules/BaseModule.php` — abstract base class
- [x] Create `app/Modules/BlockModule.php` — abstract block base class
- [x] Create `app/Modules/CptModule.php` — abstract CPT base class
- [x] Create `app/Modules/ModuleManager.php` — loader, toggle, auto-discovery
- [x] Create `app/Providers/ModuleServiceProvider.php` — wire into Sage container
- [x] Register ModuleServiceProvider in `functions.php`
- [x] Create `config/modules.php` — module paths, core modules, defaults
- [x] Create `modules/` directory with initial modules
- [x] Create `app/helpers.php` — utility functions (theme_option, get_excerpt, etc.)

### 1.2 Module Manager Admin UI
- [x] Create `modules/module-manager/module.php` — settings page module
- [x] Create settings page view with toggle UI for all modules
- [x] Add dependency validation (prevent disabling required modules)
- [x] Add admin styles for the settings page
- [ ] Add reset-to-defaults functionality
- [ ] Add JavaScript for live dependency warnings

### 1.3 Blade Templates & Layouts
- [x] Update `resources/views/layouts/app.blade.php` — base layout with header/footer
- [x] Update `resources/views/sections/header.blade.php`
- [x] Update `resources/views/sections/footer.blade.php`
- [x] Create `resources/views/partials/navigation.blade.php` (mobile menu)
- [x] Existing: `resources/views/page.blade.php`
- [x] Existing: `resources/views/single.blade.php`
- [x] Existing: `resources/views/index.blade.php` (archive)
- [x] Existing: `resources/views/404.blade.php`
- [x] Existing: `resources/views/search.blade.php`
- [x] Create `resources/views/template-full-width.blade.php`

### 1.4 Design Token System
- [x] Set up Tailwind 4 theme config in `resources/css/app.css` (CSS variables)
- [x] Define color tokens (primary, secondary)
- [x] Define typography tokens (font families)
- [ ] Define spacing/layout tokens
- [x] Create `resources/css/editor.css` with matching editor styles
- [ ] Configure `theme.json` with design tokens for block editor

### 1.5 Vite Build Configuration
- [x] Update `vite.config.js` with correct theme path and module watching
- [ ] Configure module editor script compilation (per-block entry points)
- [x] Add watcher for module Blade templates
- [ ] Verify HMR works with Blade templates

### 1.6 Core Utility Modules
- [x] Create `modules/theme-options/module.php` — replaced by site-settings module
- [x] Create `modules/white-label/module.php` — admin branding, login page customization
- [x] Create `modules/seo/module.php` — meta tags, Open Graph, schema markup

### 1.7 GitHub Release System
- [x] Create `.distignore` — files to exclude from release zip
- ~~Create `.github/workflows/release.yml`~~ — not needed (manual deploys)
- ~~Add update checker~~ — not needed

---

## Phase 2: Core Blocks (v1.1)

### 2.1 Block Infrastructure
- [x] Register custom block category (`fluxstack`) — in ModuleServiceProvider
- [x] Create `app/Modules/BlockRenderer.php` — Blade-compatible render helper
- [x] Create `modules/_template/` — scaffolding template for new blocks
- [x] Document block creation workflow (README in _template)

### 2.2 Layout Blocks
- [x] `modules/hero-block/` — full-width hero with background, heading, CTAs (PHP-only)
- [x] `modules/cta-block/` — call-to-action banner (PHP-only)
- [x] `modules/section-wrapper/` — generic section with background options (PHP-only)

### 2.3 Content Blocks
- [x] `modules/feature-grid/` — grid of feature cards with icons (PHP-only)
- [x] `modules/icon-box/` — single icon + text block (PHP-only)
- [x] `modules/accordion-block/` — FAQ/accordion component (PHP-only + vanilla JS)
- [x] `modules/stats-counter/` — animated number counters (PHP-only + vanilla JS)

### 2.4 Media Blocks
- [ ] `modules/image-gallery/` — filterable image gallery (port from FluxStack)
- [ ] `modules/testimonial-slider/` — testimonial carousel

---

## Phase 3: CPT Modules (v1.2)

Port from FluxStack with refactoring to use BaseModule pattern:

### 3.1 CPT Infrastructure
- [x] Create `app/Modules/CptModule.php` — abstract CPT base class (handles CPT + taxonomy registration boilerplate)
- [x] Add ACF JSON load/save point handling in base class
- [x] Add admin column helpers in base class

### 3.2 CPT Modules
- [x] `modules/testimonials/module.php` — port from FluxStack
- [x] `modules/teams/module.php` — port from FluxStack
- [x] `modules/services/module.php` — port from FluxStack
- [x] `modules/portfolio/module.php` — port from FluxStack
- [x] `modules/publications/module.php` — port from FluxStack
- [x] `modules/news-archives/module.php` — port from FluxStack

### 3.3 CPT Templates
- [ ] Create archive/single Blade templates for each CPT
- [ ] Create related blocks for each CPT (grid/card/slider)

---

## Phase 4: Advanced (v2.0)

- [ ] Block patterns library (pre-built page sections)
- [ ] WP-CLI commands for scaffolding modules (`wp flavor make:block`, `wp flavor make:cpt`)
- [ ] Performance module (asset optimization, lazy loading)
- [ ] Analytics module (GTM/GA4 integration)
- [ ] Full Site Editing (FSE) compatibility exploration

---

## Current Status

**Completed:**
- Sage 11 scaffolded in `wp-content/themes/fluxstack/`
- Composer dependencies installed
- npm dependencies installed (Vite, Tailwind 4)
- Vite build passes
- Module system core classes (BaseModule, BlockModule, CptModule, ModuleManager, BlockRenderer)
- ModuleServiceProvider wired into Sage container
- Module config (`config/modules.php`)
- Module Manager admin UI (modern tabbed interface, AJAX save, toast notifications)
- Site Settings module with sub-pages (General, Header, Footer, Home Page)
- White Label module (admin colors, login branding, cleanup, custom CSS)
- SEO module (meta tags, Open Graph, JSON-LD schema, head cleanup)
- All CPT modules ported with ACF JSON (testimonials, teams, services, portfolio, publications, news-archives)
- Blade templates (layout, header with mobile toggle, footer, mobile nav, full-width template)
- Design tokens set up (Tailwind 4 @theme)
- Helpers file (theme_option, site_setting, get_excerpt, get_thumbnail_url)
- .distignore for release builds
- Block infrastructure (BlockRenderer, block category, webpack config)
- Hero Block and CTA Block with compiled editor scripts
- Documentation (README, docs/architecture, docs/development, docs/deployment, docs/modules)
- CHANGELOG with full version history

**Next up:**
- Section Wrapper block
- Content blocks (feature-grid, icon-box, accordion)
- CPT archive/single Blade templates
- CPT-related blocks (testimonial-card, team-grid, etc.)
- Block patterns library

---

## Notes

- Theme name: **FluxStack**
- Text domain: `fluxstack`
- Option prefix: `fluxstack_`
- Block namespace: `fluxstack/`
- Sage 11 uses Vite instead of Bud — simpler config, faster builds, native ESM
- Tailwind 4 uses CSS-first configuration (no `tailwind.config.js` needed)
- ACF PRO remains optional but recommended for theme options and CPT fields
- Each project forks this repo as a starting point
- Current Bricks-based FluxStack moves to `bricks-legacy` branch, this becomes `main`
