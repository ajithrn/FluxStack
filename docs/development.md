# Development

## Setup

```bash
cd wp-content/themes/
git clone git@github.com:ajithrn/FluxStack.git fluxstack
cd fluxstack
composer install
npm install
npm run build
```

## Build Commands

| Command | Purpose |
|---------|---------|
| `npm run dev` | Vite dev server with HMR |
| `npm run build` | Production build (Vite only) |

That's it. PHP-only blocks need no compilation. JSX blocks are compiled as part of the Vite build via `resources/js/editor.js` imports. React uses classic JSX runtime (`React.createElement`) — externalized to WP globals along with all `@wordpress/*` packages.

## Environment & Blade Templates

### How Blade compilation works

Blade templates (`.blade.php`) are **not** compiled by `npm run build`. They compile automatically at runtime:

1. First page request → Acorn compiles `.blade.php` → PHP and caches in `wp-content/cache/acorn/framework/views/`
2. Subsequent requests → serves cached version if source file hasn't changed
3. If source file is newer → recompiles automatically

### Environment file

The `.env` file in the theme root controls the environment:

```
WP_ENV=development
APP_URL=http://your-site.local
```

Set `WP_ENV=development` for local dev — this ensures Blade always checks for template changes.

### Clearing the view cache

If templates appear stale (raw Blade syntax showing in browser), clear the compiled views:

```bash
# Option 1: WP-CLI (if available)
wp acorn view:clear

# Option 2: Manual delete
rm -f wp-content/cache/acorn/framework/views/*.php
```

This is common in Docker-based environments (DevKinsta, Local, etc.) where file timestamps may not propagate correctly.

### Production

For production, pre-compile views for performance:

```bash
wp acorn view:cache
wp acorn optimize
```

## Creating Modules

### Feature Module

```bash
mkdir modules/my-feature
```

`modules/my-feature/module.php`:

```php
<?php
use App\Modules\BaseModule;

return new class extends BaseModule {
    public function id(): string { return 'my-feature'; }
    public function name(): string { return 'My Feature'; }
    public function description(): string { return 'What this does.'; }
    public function category(): string { return 'feature'; }

    public function register(): void {
        add_action('init', [$this, 'setup']);
    }

    public function setup(): void { /* your code */ }
};
```

### CPT Module

```php
<?php
use App\Modules\CptModule;

return new class extends CptModule {
    public function id(): string { return 'events'; }
    public function name(): string { return 'Events'; }
    public function description(): string { return 'Event management.'; }
    public function postType(): string { return 'event'; }
    public function labels(): array { return ['name' => 'Events', 'singular_name' => 'Event']; }
    public function postTypeArgs(): array { return ['menu_icon' => 'dashicons-calendar', 'rewrite' => ['slug' => 'events']]; }
    public function taxonomies(): array {
        return ['event_type' => ['labels' => ['name' => 'Types'], 'args' => ['rewrite' => ['slug' => 'event-type']]]];
    }
};
```

CPT defaults: no block editor (`show_in_rest => false`), no content area (supports: title, thumbnail, excerpt, custom-fields). Override in `postTypeArgs()` if needed.

### CPT Templates & Styles

CPT modules ship skeleton templates and CSS. On first activation, these are copied to the theme for per-project customization:

```
modules/my-cpt/
├── module.php
├── acf-json/
├── views/                              ← Skeleton templates
│   ├── archive-{post-type}.blade.php
│   └── single-{post-type}.blade.php
├── styles/                             ← Skeleton CSS
│   └── {module-id}.css
└── blocks/
```

**On boot, files are copied to:**
- `resources/views/archive-{post-type}.blade.php`
- `resources/views/single-{post-type}.blade.php`
- `resources/css/modules/{module-id}.css`

**Important:** Files are only copied if they don't already exist. Your customizations are never overwritten. To reset to defaults, delete the file and reactivate the module.

After enabling a new CPT module, run `npm run build` to include its CSS.

**Example archive template:**

```blade
@extends('layouts.app')

@section('content')
  <div class="page-header container">
    <h1 class="page-header__title">{{ post_type_archive_title('', false) }}</h1>
  </div>

  <div class="post-grid container">
    @while(have_posts()) @php(the_post())
      <article @php(post_class('post-card'))>
        {{-- Card content --}}
      </article>
    @endwhile
  </div>
@endsection
```

## Creating Blocks

### PHP-Only Block (recommended for most blocks)

Requires WordPress 7.0+. No JavaScript, no build step.

```bash
mkdir modules/my-block
```

`modules/my-block/module.php`:

```php
<?php
use App\Modules\BaseModule;

return new class extends BaseModule {
    public function id(): string { return 'my-block'; }
    public function name(): string { return 'My Block'; }
    public function description(): string { return 'A simple block.'; }
    public function category(): string { return 'block'; }
    public function enabledByDefault(): bool { return true; }

    public function register(): void {
        add_action('init', [$this, 'registerBlock']);
        add_action('enqueue_block_editor_assets', [$this, 'editorStyles']);
    }

    public function registerBlock(): void {
        register_block_type('fluxstack/my-block', [
            'title'    => 'My Block',
            'category' => 'fluxstack',
            'icon'     => 'block-default',
            'attributes' => [
                'heading' => ['type' => 'string', 'default' => '', 'label' => 'Heading'],
                'text'    => ['type' => 'string', 'default' => '', 'label' => 'Text'],
                'variant' => ['type' => 'string', 'enum' => ['Primary', 'Secondary'], 'default' => 'Primary', 'label' => 'Style'],
                'showBorder' => ['type' => 'boolean', 'default' => false, 'label' => 'Show Border'],
            ],
            'supports' => [
                'autoRegister' => true,
                'align' => ['wide', 'full'],
                'color' => ['text' => true, 'background' => true],
                'spacing' => ['padding' => true],
            ],
            'render_callback' => [$this, 'render'],
        ]);

        wp_enqueue_block_style('fluxstack/my-block', [
            'handle' => 'fluxstack-my-block',
            'src'    => get_theme_file_uri('modules/my-block/style.css'),
            'path'   => get_theme_file_path('modules/my-block/style.css'),
        ]);
    }

    public function render(array $attributes): string {
        $wrapper = get_block_wrapper_attributes(['class' => 'my-block']);
        return sprintf('<div %s><h2>%s</h2><p>%s</p></div>', $wrapper, esc_html($attributes['heading']), esc_html($attributes['text']));
    }

    public function editorStyles(): void {
        wp_enqueue_style('fluxstack-my-block', get_theme_file_uri('modules/my-block/style.css'));
    }
};
```

`modules/my-block/style.css` — your block styles.

**Auto-generated controls:**

| Attribute type | Editor control |
|---|---|
| `string` | TextControl |
| `string` + `enum` | SelectControl |
| `integer` / `number` | NumberControl |
| `boolean` | ToggleControl |

**Important rules:**
- Never name an attribute `style`, `className`, `textColor`, `backgroundColor`, `fontSize`, `fontFamily`, `align`, or `anchor` — these are reserved by block supports
- Enum values are used as labels — use sentence case: `['Primary', 'Secondary']`
- Add `'label'` key for explicit control labels

### JSX Block (for complex editor UI)

Only use when you need MediaUpload, InnerBlocks, repeater fields, inline RichText editing, or custom React components.

```
modules/my-complex-block/
├── module.php       ← BlockModule class (with render_callback)
├── block.json       ← Block metadata (attributes, supports)
├── editor.jsx       ← JSX editor UI
└── style.css        ← Shared styles (frontend + editor)
```

`module.php` extends `BlockModule` and registers via `register_block_type($this->path(), ['render_callback' => ...])`.

Import the JSX in `resources/js/editor.js`:

```js
import '../../modules/my-complex-block/editor.jsx';
```

Vite compiles it with `@vitejs/plugin-react` (classic JSX runtime). WordPress packages are externalized (loaded from WP globals).

**Key notes for JSX blocks:**
- Use `InspectorControls` with `group="styles"` to add controls to the Styles tab
- Use plain HTML `<button>` with Unicode characters for canvas actions (dashicons don't load in editor iframe)
- Use `useState` from `@wordpress/element` for editor-only UI state
- Enqueue `style.css` via `enqueue_block_editor_assets` for editor canvas styling
- The `render_callback` in `module.php` handles frontend output (no `render.php` needed)

## Site Settings Sub-Pages

```bash
mkdir -p modules/site-settings/my-page/views
```

`modules/site-settings/my-page/module.php`:

```php
<?php
return [
    'id' => 'site-settings-my-page',
    'title' => 'My Page',
    'slug' => 'fluxstack-site-my-page',
    'priority' => 40,
    'default' => false,
    'description' => 'Description shown in Module Manager.',
    'callback' => function () {
        $settings = get_option('fluxstack_site_settings', []);
        include __DIR__ . '/views/page.php';
    },
];
```

## Accessing Settings in Templates

```php
use function App\site_setting;

site_setting('phone');
site_setting('logo');
site_setting('social_facebook');
str_replace('{year}', date('Y'), site_setting('copyright'));
```

## Sage Conventions to Follow

- **View Composers** (`app/View/Composers/`) — pass data to Blade views
- **Components** (`app/View/Components/` + `resources/views/components/`) — reusable UI elements
- **Template hierarchy** — `resources/views/` follows WordPress template hierarchy (e.g. `archive-portfolio.blade.php`)
- **`theme.json`** — auto-generated from Tailwind, don't edit manually
- **Fonts** — place `.woff2` in `resources/fonts/`, define `@font-face` in `resources/css/fonts.css`

## References

- [Sage 11 Docs](https://roots.io/sage/docs/)
- [PHP-Only Block Registration](https://make.wordpress.org/core/2026/03/03/php-only-block-registration/) — official WP 7.0 dev note
- [Trac #64639](https://core.trac.wordpress.org/ticket/64639) — autoRegister implementation
- [Block Supports Reference](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/)
- [Gutenberg PR #71794](https://github.com/WordPress/gutenberg/pull/71794) — initial PHP-only implementation


---

## Sage 11 Quick Reference

### Key Files

| File | Purpose |
|------|---------|
| `app/setup.php` | Theme support, menus, sidebars, asset enqueueing |
| `app/filters.php` | WordPress filters |
| `app/helpers.php` | Global helper functions |
| `app/Providers/` | Service providers (Laravel pattern) |
| `app/View/Composers/` | Pass data to specific Blade views |
| `app/View/Components/` | Reusable Blade component classes |
| `resources/views/` | Blade templates (follows WP template hierarchy) |
| `resources/css/app.css` | Main stylesheet (Tailwind) |
| `resources/css/editor.css` | Block editor styles |
| `resources/js/app.js` | Frontend JavaScript |
| `resources/js/editor.js` | Block editor scripts (styles, variants, JSX imports) |
| `theme.json` | Auto-generated from Tailwind — don't edit manually |
| `vite.config.js` | Build configuration |

### View Composers

Pass data to Blade views without polluting templates:

```php
// app/View/Composers/PageHeader.php
namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class PageHeader extends Composer
{
    protected static $views = ['partials.page-header'];

    public function with() {
        return ['title' => get_the_title()];
    }
}
```

Auto-matched by name: `PageHeader` composer → `partials/page-header.blade.php`

### Components

Reusable UI elements with class + template:

```blade
{{-- Usage --}}
<x-alert type="warning" message="Something happened" />
```

```php
// app/View/Components/Alert.php
namespace App\View\Components;
use Roots\Acorn\View\Component;

class Alert extends Component {
    public function __construct(public string $type = 'info', public string $message = '') {}
}
```

```blade
{{-- resources/views/components/alert.blade.php --}}
<div class="alert alert--{{ $type }}">{{ $message }}</div>
```

### Template Hierarchy

Blade files in `resources/views/` follow WordPress template hierarchy:

- `front-page.blade.php` — static front page
- `home.blade.php` — blog/posts page
- `archive-{post-type}.blade.php` — CPT archive
- `single-{post-type}.blade.php` — CPT single
- `page-{slug}.blade.php` — specific page by slug
- `template-{name}.blade.php` — custom page template (add `Template Name:` comment)

### Tailwind + theme.json

Tailwind config auto-generates `theme.json` on build. Define design tokens in `resources/css/app.css`:

```css
@theme {
    --color-primary-500: #3b82f6;
    --color-primary-600: #2563eb;
    --font-sans: "Inter", system-ui, sans-serif;
}
```

These become available in the block editor color/font pickers.

### Fonts

1. Place `.woff2` files in `resources/fonts/`
2. Create `resources/css/fonts.css` with `@font-face` declarations
3. Import in both `app.css` and `editor.css`: `@import './fonts.css';`
4. Use `@fonts` alias: `src: url('@fonts/my-font.woff2')`

### WP-CLI Commands

```bash
wp acorn optimize          # Cache config + views (production)
wp acorn view:cache        # Pre-compile Blade templates
wp acorn view:clear        # Clear compiled templates
wp acorn make:composer Name  # Create View Composer
wp acorn make:component Name # Create Component
```

### Assets in Templates

```blade
{{-- Images --}}
<img src="{{ Vite::asset('resources/images/logo.svg') }}">

{{-- In CSS --}}
background-image: url("@images/hero.jpg");
```


---

## Maintenance Notes

### Updating Sage/Acorn

```bash
composer update roots/acorn
```

This only updates `vendor/roots/acorn/`. Your code in `app/`, `modules/`, `resources/` is never touched. After updating:

1. Verify `functions.php` boot process works (check if `Application::configure()` API changed)
2. Verify `ThemeServiceProvider extends SageServiceProvider` still valid
3. Test template resolution and Vite asset loading
4. Clear view cache: `rm -f wp-content/cache/acorn/framework/views/*.php`

### Module Error Isolation

Modules are wrapped in try/catch during `register()` and `boot()`. If a module fails, it's skipped and an error is logged to PHP's error log. The rest of the site continues working.

Check logs: `tail -f /path/to/error.log | grep FluxStack`

### Scaffold Caching

CPT module scaffolding (copying views/CSS) only runs once per module. The flag is stored as `fluxstack_scaffolded_{module-id}` in `wp_options`. To force re-scaffolding:

```php
// Delete the flag, then reload the page
delete_option('fluxstack_scaffolded_portfolio');
```

Or toggle the module off/on in Module Manager (this resets the flag automatically).

### Config Priority

`config/modules.php` defines:
- `core` — modules that can never be disabled (module-manager, site-settings)
- `defaults` — modules enabled on fresh installations
- `paths` — directories to scan for modules

### Vite Base Path

The `base` path in `vite.config.js` defaults to `/app/themes/fluxstack/public/build/`. Override via `.env`:

```
VITE_BASE=/wp-content/themes/fluxstack/public/build/
```

### CSS Module Glob Import

`app.css` uses `@import "./modules/*.css"` which is resolved by the custom `cssGlobImport` Vite plugin. This plugin uses `readdirSync` (Node 20 compatible). If the `modules/` directory is empty, it outputs a comment and continues.

### Design Tokens

All visual customization starts in `resources/css/config.css`. This file defines:
- Colors (primary + secondary palettes)
- Typography (font families)
- Layout (container widths, padding)
- Borders (radius scale)
- Transitions (speed scale)
- Dark mode tokens (reserved, unused by default)

Both `app.css` and `editor.css` import `config.css` so changes propagate everywhere.

### Button Component

Use `.btn` classes for consistent buttons:
```html
<a href="#" class="btn btn--primary">Primary</a>
<a href="#" class="btn btn--outline btn--sm">Small Outline</a>
```

Available: `--primary`, `--secondary`, `--outline`, `--ghost`, `--sm`, `--lg`, `--block`
