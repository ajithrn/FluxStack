# Developer Documentation

## Architecture

FluxStack uses a modular architecture where each feature is encapsulated in a self-contained module under `modules/`. Modules are loaded and initialized by `modules/modules.php`, which is called from `functions.php`.

### Module Types

| Type | Base Class | Examples |
|------|-----------|----------|
| **Core** | None (standalone) | Bricks, Utility Functions, Theme Options, Dynamic Snippets, White Label |
| **Content (CPT)** | `FS_Base_CPT_Module` | Services, Teams, Publications, Portfolio, Testimonials, Image Gallery |
| **Standalone** | None | News Archives |

### Load Order

```
functions.php
└── modules/modules.php
    ├── module-manager/module-manager.php  (always loaded first)
    ├── base/base-cpt-module.php          (base class for CPT modules)
    ├── Core modules                       (Bricks, Utils, Theme Options, etc.)
    ├── Content modules                    (Services, Teams, etc.)
    └── Standalone modules                 (News Archives)
```

Each module is conditionally loaded via `FS_Module_Manager::can_load_module()`.

---

## Creating a New Module

### 1. Content Module (Custom Post Type)

Create a new directory under `modules/` and extend `FS_Base_CPT_Module`:

```
modules/your-module/
├── your-module.php          # Module class
└── acf-json/                # ACF field group JSON files
    └── group_your_fields.json
```

**Module file template:**

```php
<?php
/**
 * Your Module
 *
 * @package FluxStack
 */

class FS_Your_Module extends FS_Base_CPT_Module {
    protected static function get_module_dir() {
        return 'your-module';
    }

    protected static function get_acf_group_id() {
        return 'group_your_fields';
    }

    protected static function get_post_type_config() {
        return array(
            'post_type'     => 'your_type',
            'slug'          => 'your-type',
            'menu_icon'     => 'dashicons-admin-post',
            'menu_position' => 20,
            'supports'      => array('title', 'thumbnail', 'custom-fields'),
            'labels'        => array(
                'name'               => 'Your Items',
                'singular_name'      => 'Your Item',
                'menu_name'          => 'Your Items',
                // ... full labels array
            ),
        );
    }

    // Optional: Add taxonomy
    protected static function get_taxonomy_config() {
        return array(
            array(
                'taxonomy' => 'your_category',
                'slug'     => 'your-category',
                'labels'   => array(/* ... */),
            ),
        );
    }

    // Optional: Add admin columns
    protected static function get_custom_columns() {
        return array(
            'thumbnail'    => __('Thumbnail', 'fluxstack'),
            'custom_field' => __('Custom Field', 'fluxstack'),
        );
    }

    // Optional: Render custom column content
    public static function render_column($column, $post_id) {
        switch ($column) {
            case 'custom_field':
                echo esc_html(get_field('your_field', $post_id));
                break;
            default:
                parent::render_column($column, $post_id);
                break;
        }
    }
}
```

### 2. Register the Module

1. Add the module to `modules/module-manager/module-manager.php` in the `$default_modules` array:

```php
'your-module' => false, // disabled by default
```

1. Add it to a module group in `register_module_groups()`:

```php
'your-module' => array(
    'id'          => 'your-module',
    'name'        => 'Your Module',
    'description' => 'Adds a custom post type for your items.',
    'default'     => false,
),
```

1. Add the load entry in `modules/modules.php`:

```php
if (FS_Module_Manager::can_load_module('your-module')) {
    require_once dirname( __FILE__ ) . '/your-module/your-module.php';
    FS_Your_Module::init();
}
```

### 3. Core Module (Non-CPT)

For modules that don't manage a CPT, create a standalone class without extending `FS_Base_CPT_Module`:

```php
<?php
class FS_Your_Core_Module {
    public static function init() {
        // Register hooks and filters
    }
}
```

---

## ACF Integration

### ACF JSON Workflow

Each module stores its ACF field groups as JSON files in its own `acf-json/` directory:

```
modules/services/acf-json/group_services_meta_fields.json
modules/teams/acf-json/group_teams_meta_fields.json
```

**How it works:**

- **Loading:** The base class registers each module's `acf-json/` dir as an ACF JSON load point
- **Saving:** When you edit a field group in the ACF UI, changes are saved back to the correct module's `acf-json/` directory (matched by ACF group ID)
- **Version control:** JSON files should be committed to git for reproducible deployments

### Creating ACF Fields

1. Enable the module in FluxStack Settings
2. Go to **Custom Fields > Add New**
3. Create your field group and assign it to your CPT
4. Save — the JSON file will be created in the module's `acf-json/` directory
5. Commit the JSON file to version control

### ACF Options Pages (Theme Options)

The `theme-options` module manages ACF options pages with a **universal base + per-client** pattern:

**Universal pages** (always registered):

- **Theme Options** — Branding, Contact Info, Social Media, SEO
- **Footer** — Copyright text, CTA button

**Conditional pages** (auto-registered when matching ACF JSON exists in `acf-json/custom/`):

- **Header** — registered if a field group targets `acf-options-header`
- **Home Page** — registered if a field group targets `acf-options-home-page`

### Per-Client ACF Fields

Site-specific field groups go in `modules/theme-options/acf-json/custom/`:

```text
modules/theme-options/acf-json/
├── group_fluxstack_general_settings.json   # Base (universal)
├── group_fluxstack_footer_settings.json    # Base (universal)
└── custom/                                  # Per-client
    ├── .gitkeep
    └── group_fluxstack_home_settings.json  # Client-specific
```

**Workflow:**

1. Create a field group in ACF admin targeting a conditional options page
2. Save — JSON auto-saves to `acf-json/custom/`
3. The sub-page registers automatically on next load
4. Commit the JSON for this client's deployment

---

## Dynamic Snippets (Bricks Dynamic Data)

Dynamic snippets are **data tags** — not blocks. They let you pull Theme Options values into any Bricks element using the dynamic data picker.

### Using Snippets in Bricks Templates

1. Open a Bricks template and select any element (Text, Heading, Image, etc.)
2. Click the **dynamic data icon** `{⚡}` in the content/field input
3. Look under the **FluxStack** category
4. Select a snippet — it inserts a `{tag}` that renders the live value

### Available Snippets

| Snippet | Output | Why It Exists |
| --- | --- | --- |
| `social_links` | Social media icon links (full HTML) | Loops through repeater, renders icons — can't do with a single ACF tag |
| `copyright` | Copyright text with dynamic year | Replaces `{year}` and `{site_name}` placeholders — raw ACF returns unprocessed text |

> **Note:** For simple fields like `contact_email`, `contact_phone`, `site_logo`, etc., use Bricks' **native ACF dynamic data** (e.g., `{acf_contact_email}`). Custom snippets are only needed when you need processing beyond raw field values.

### SEO Meta (Automatic)

The SEO fields in Theme Options → SEO tab are **not** dynamic snippets. They output automatically in `<head>` via `wp_head`:

- Default OG image (fallback for pages without featured images)
- Meta description (for non-singular pages)
- Google Site Verification meta tag
- Google Analytics / GTM script (auto-detects G-, UA-, or GTM- prefix)
- Additional head scripts (tracking pixels, etc.)

---

### Creating New Snippets (Developer)

#### Universal Snippets

Located in `modules/dynamic-snippets/snippets/`, each file returns a config array:

```php
<?php
return array(
    'name'     => 'contact_email',
    'label'    => __('Contact Email', 'fluxstack'),
    'category' => 'fluxstack',
    'render'   => function() {
        return FS_Utils::get_theme_option('contact_email');
    },
);
```

Drop a new `.php` file in the `snippets/` directory — it's auto-discovered, no need to edit `dynamic-snippets.php`.

#### Module-Owned Snippets

Content modules register their own Bricks snippets inside their `register_hooks()`:

```php
// In FS_Your_Module::register_hooks()
add_filter('bricks/dynamic_data/register_snippets', function($snippets) {
    $snippets['fluxstack_your_snippet'] = array(
        'name'     => 'your_snippet',
        'label'    => 'Your Snippet',
        'category' => 'fluxstack',
        'render'   => array(__CLASS__, 'render_snippet'),
    );
    return $snippets;
});
```

This keeps snippets independent — module off = snippets don't exist.

---

## Bricks Builder Integration

### Custom Elements

Place custom Bricks elements in `modules/bricks/elements/`:

```
modules/bricks/elements/
└── your-element.php
```

Elements are auto-discovered and registered. Each element file must contain a class extending `\Bricks\Element`.

**Example:**

```php
<?php
if (!defined('ABSPATH')) exit;

class Element_Your_Element extends \Bricks\Element {
    public $category = 'fluxstack';
    public $name     = 'your-element';
    public $icon     = 'ti-widget';

    public function get_label() {
        return 'Your Element';
    }

    public function set_controls() {
        // Define element controls
    }

    public function render() {
        // Render element output
    }
}
```

### Utility Methods

The `FS_Bricks` class provides helper methods:

- `FS_Bricks::is_bricks_template()` — Check if using Bricks template
- `FS_Bricks::is_builder_active()` — Check if Bricks editor is open
- `FS_Bricks::get_template_id($post_id)` — Get the Bricks template ID

---

## Native Blocks

WordPress blocks are located in `blocks/` and auto-discovered by `functions.php`:

```
blocks/
├── your-block/
│   ├── block.json              # Block metadata (with dependencies)
│   ├── your-block.php          # Server-side render callback
│   ├── your-block.js           # Editor script
│   └── your-block.css          # Block styles
```

Blocks can declare module dependencies in `block.json`:

```json
{
    "fluxstack": {
        "requiredModules": ["services"],
        "requiredBlocks": []
    }
}
```

---

## Coding Standards

### Naming Conventions

- **Classes:** `FS_` prefix (e.g., `FS_Services`, `FS_Base_CPT_Module`)
- **Constants:** Uppercase with underscores (e.g., `OPTION_NAME`)
- **Functions:** Lowercase with underscores (e.g., `fluxstack_get_theme_option`)
- **Hooks:** Use `array(__CLASS__, 'method_name')` or `array(static::class, 'method_name')` pattern

### File Structure

- One class per file
- File name matches module directory name
- ACF JSON in `acf-json/` subdirectory
- Assets in `assets/css/` and `assets/js/` subdirectories

### WordPress Coding Standards

- Use `esc_html()`, `esc_attr()`, `esc_url()` for output escaping
- Use `sanitize_text_field()`, `absint()` for input sanitization
- Use `wp_nonce_field()` / `wp_verify_nonce()` for form security
- Use `__()` and `_x()` with `'fluxstack'` text domain for all strings

---

## White Label Development

The white-label module includes admin styles in `modules/white-label/assets/css/admin.min.css`.

To modify the admin styling:

1. Edit the source CSS
2. Minify and save as `admin.min.css`
3. The styles are automatically loaded on all admin and login pages
