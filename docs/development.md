# Development

## Setup

```bash
# Clone the theme into your WordPress themes directory
cd wp-content/themes/
git clone git@github.com:ajithrn/FluxStack.git fluxstack

# Install dependencies
cd fluxstack
composer install
npm install

# Build for development
npm run dev        # Vite dev server (HMR for theme assets)
npm run dev:blocks # Watch mode for block editor scripts
```

## Build Commands

| Command | Purpose |
|---------|---------|
| `npm run dev` | Vite dev server with HMR |
| `npm run build` | Production build (Vite + blocks) |
| `npm run build:blocks` | Compile block editor scripts only |
| `npm run dev:blocks` | Watch mode for blocks |

## Creating a New Module

### Feature Module

```bash
mkdir modules/my-feature
```

Create `modules/my-feature/module.php`:

```php
<?php
use App\Modules\BaseModule;

return new class extends BaseModule {
    public function id(): string { return 'my-feature'; }
    public function name(): string { return 'My Feature'; }
    public function description(): string { return 'What this module does.'; }
    public function category(): string { return 'feature'; }
    public function enabledByDefault(): bool { return false; }

    public function register(): void
    {
        add_action('init', [$this, 'setup']);
    }

    public function setup(): void
    {
        // Your code here
    }
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

    public function labels(): array {
        return ['name' => 'Events', 'singular_name' => 'Event', ...];
    }

    public function postTypeArgs(): array {
        return ['menu_icon' => 'dashicons-calendar', 'rewrite' => ['slug' => 'events']];
    }

    public function taxonomies(): array {
        return [
            'event_type' => [
                'labels' => ['name' => 'Event Types', 'singular_name' => 'Event Type'],
                'args' => ['rewrite' => ['slug' => 'event-type']],
            ],
        ];
    }
};
```

### Block Module

1. Create the module directory:
```bash
mkdir modules/my-block
```

2. Create `module.php`:
```php
<?php
use App\Modules\BlockModule;

return new class extends BlockModule {
    public function id(): string { return 'my-block'; }
    public function name(): string { return 'My Block'; }
    public function description(): string { return 'A custom block.'; }
    public function blockName(): string { return 'fluxstack/my-block'; }
    public function enabledByDefault(): bool { return true; }
};
```

3. Create `block.json`, `editor.js`, `render.php`, and `style.css`
4. Run `npm run build:blocks` to compile

## Adding a Site Settings Sub-Page

Create a folder inside `modules/site-settings/`:

```bash
mkdir -p modules/site-settings/my-page/views
```

Create `modules/site-settings/my-page/module.php`:

```php
<?php
return [
    'id' => 'site-settings-my-page',
    'title' => 'My Page',
    'slug' => 'fluxstack-site-my-page',
    'priority' => 40,
    'default' => false,
    'description' => 'Description for the module manager.',
    'callback' => function () {
        $settings = get_option('fluxstack_site_settings', []);
        include __DIR__ . '/views/page.php';
    },
];
```

Create the view file with the standard FluxStack admin layout.

## Accessing Settings in Templates

```php
// In Blade templates or PHP
use function App\site_setting;

$phone = site_setting('phone');
$logo = site_setting('logo');
$copyright = str_replace('{year}', date('Y'), site_setting('copyright'));
```

## Coding Standards

- PSR-4 autoloading via Composer (`App\` namespace)
- WordPress coding standards for hooks and filters
- Anonymous classes for modules (no separate class files needed)
- Blade templates for frontend rendering
- Tailwind utility classes for styling
