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

That's it. PHP-only blocks need no compilation. JSX blocks (if any) are compiled as part of the Vite build via `resources/js/editor.js` imports.

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

Only use when you need MediaUpload, InnerBlocks, or custom React components.

```
modules/my-complex-block/
├── module.php       ← BlockModule class
├── block.json       ← Block metadata (no editorScript field)
├── editor.jsx       ← JSX editor UI
├── render.php       ← Server-side render
└── style.css
```

Import the JSX in `resources/js/editor.js`:

```js
import '../../modules/my-complex-block/editor.jsx';
```

Vite compiles it with `@vitejs/plugin-react`. WordPress packages are externalized (loaded from WP globals).

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
