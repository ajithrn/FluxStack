# Blocks Guide

## Two Methods

FluxStack supports two block development methods. Choose based on the editor experience needed.

---

## Method 1: PHP-Only Blocks

**Requires:** WordPress 7.0+ (or Gutenberg plugin 21.8+)
**Files:** `module.php` + `style.css`
**Build step:** None

### How It Works

Add `'autoRegister' => true` to `supports`. WordPress auto-generates editor sidebar controls from your `attributes` array and uses `ServerSideRender` for the editor preview.

### Complete Example

```php
<?php
use App\Modules\BaseModule;

return new class extends BaseModule {
    public function id(): string { return 'alert-block'; }
    public function name(): string { return 'Alert Block'; }
    public function description(): string { return 'A notice/alert message.'; }
    public function category(): string { return 'block'; }
    public function enabledByDefault(): bool { return true; }

    public function register(): void {
        add_action('init', [$this, 'registerBlock']);
        add_action('enqueue_block_editor_assets', [$this, 'editorStyles']);
    }

    public function registerBlock(): void {
        register_block_type('fluxstack/alert', [
            'title'    => 'Alert',
            'category' => 'fluxstack',
            'icon'     => 'warning',

            'attributes' => [
                'message' => [
                    'type'    => 'string',
                    'default' => 'This is an important notice.',
                    'label'   => 'Message',
                ],
                'severity' => [
                    'type'    => 'string',
                    'enum'    => ['Info', 'Warning', 'Error', 'Success'],
                    'default' => 'Info',
                    'label'   => 'Severity',
                ],
                'dismissible' => [
                    'type'    => 'boolean',
                    'default' => false,
                    'label'   => 'Dismissible',
                ],
            ],

            'supports' => [
                'autoRegister' => true,
                'align'        => ['wide', 'full'],
                'color'        => ['text' => true, 'background' => true],
                'spacing'      => ['padding' => true],
                'border'       => ['radius' => true],
            ],

            'render_callback' => [$this, 'render'],
        ]);

        wp_enqueue_block_style('fluxstack/alert', [
            'handle' => 'fluxstack-alert',
            'src'    => get_theme_file_uri('modules/alert-block/style.css'),
            'path'   => get_theme_file_path('modules/alert-block/style.css'),
        ]);
    }

    public function render(array $attributes): string {
        $severity = strtolower($attributes['severity']);
        $wrapper = get_block_wrapper_attributes([
            'class' => 'fluxstack-alert fluxstack-alert--' . esc_attr($severity),
            'role'  => 'alert',
        ]);

        return sprintf(
            '<div %s><p>%s</p></div>',
            $wrapper,
            esc_html($attributes['message'])
        );
    }

    public function editorStyles(): void {
        wp_enqueue_style('fluxstack-alert', get_theme_file_uri('modules/alert-block/style.css'));
    }
};
```

### Auto-Generated Controls

| Attribute Type | Editor Control |
|---|---|
| `'type' => 'string'` | TextControl |
| `'type' => 'string'` + `'enum' => [...]` | SelectControl |
| `'type' => 'integer'` or `'number'` | NumberControl |
| `'type' => 'boolean'` | ToggleControl |

### Block Supports (native panels)

```php
'supports' => [
    'autoRegister' => true,
    'align'        => ['wide', 'full'],
    'color'        => ['text' => true, 'background' => true],
    'typography'   => ['fontSize' => true, 'lineHeight' => true, 'fontWeight' => true],
    'spacing'      => ['margin' => true, 'padding' => true],
    'border'       => ['color' => true, 'radius' => true, 'style' => true, 'width' => true],
],
```

These unlock native Color, Typography, Spacing, and Border panels. `get_block_wrapper_attributes()` applies them automatically.

### Rules & Gotchas

**Reserved attribute names (never use these):**
`style`, `className`, `textColor`, `backgroundColor`, `gradient`, `fontSize`, `fontFamily`, `align`, `anchor`

**Enum values are used as labels** — use sentence case: `['Primary', 'Secondary', 'Dark']`

**Editor styles workaround:** `wp_enqueue_block_style()` doesn't reliably inject styles for autoRegister blocks in the editor. Always also enqueue via `enqueue_block_editor_assets`.

**Editor placeholder previews:** When a block is inserted with empty attributes, show a placeholder with sample content so users know what to expect. Use the `renderPlaceholder()` helper from `BaseModule`:

```php
public function render(array $attributes): string {
    // Show placeholder in editor when block is empty
    if ($this->isEditorPreview() && empty($attributes['heading']) && empty($attributes['text'])) {
        $sample = '<div style="...">Sample content showing what the block looks like</div>';
        return $this->renderPlaceholder(
            'my-block-class',           // Block's BEM base class
            'Block Name',               // Title shown in placeholder
            'Instructions for the user.', // Description
            $sample                     // Optional sample HTML (rendered at 50% opacity)
        );
    }

    // Normal render...
}
```

The `isEditorPreview()` method detects `ServerSideRender` REST calls. The placeholder disappears once the user fills in any attribute via the sidebar.

**Lazy frontend JS:** Register at `init`, enqueue from `render_callback`:
```php
add_action('init', fn() => wp_register_script('my-block-js', get_theme_file_uri('modules/my-block/frontend.js'), [], '1.0', true));
// In render_callback:
wp_enqueue_script('my-block-js'); // Only loads on pages with the block
```

**CSS custom properties for accent colors:**
```php
$style = !empty($attributes['accentColor']) ? '--accent:' . esc_attr($attributes['accentColor']) . ';' : '';
$wrapper = get_block_wrapper_attributes(['style' => $style]);
```
```css
.my-block::before { background: var(--accent, currentColor); }
```

---

## Method 2: Full JSX Blocks

**Requires:** Vite build
**Files:** `module.php` + `block.json` + `editor.jsx` + `render.php` + `style.css`

### File Structure

```
modules/my-block/
├── module.php       ← BlockModule class (registers block.json path)
├── block.json       ← Block metadata (no editorScript field)
├── editor.jsx       ← React edit() component
├── render.php       ← Server-side frontend render
└── style.css        ← Styles
```

### module.php

```php
<?php
use App\Modules\BlockModule;

return new class extends BlockModule {
    public function id(): string { return 'my-block'; }
    public function name(): string { return 'My Block'; }
    public function description(): string { return 'A complex block.'; }
    public function blockName(): string { return 'fluxstack/my-block'; }
    public function enabledByDefault(): bool { return true; }
};
```

### editor.jsx

```jsx
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls, MediaUpload } from '@wordpress/block-editor';
import { PanelBody, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

registerBlockType(metadata.name, {
    edit({ attributes, setAttributes }) {
        const blockProps = useBlockProps();
        return (
            <>
                <InspectorControls>
                    <PanelBody title={__('Settings', 'fluxstack')}>
                        <MediaUpload
                            onSelect={(media) => setAttributes({ imageUrl: media.url, imageId: media.id })}
                            allowedTypes={['image']}
                            value={attributes.imageId}
                            render={({ open }) => (
                                <Button onClick={open} variant="secondary">
                                    {attributes.imageUrl ? __('Replace Image', 'fluxstack') : __('Select Image', 'fluxstack')}
                                </Button>
                            )}
                        />
                    </PanelBody>
                </InspectorControls>
                <div {...blockProps}>
                    {attributes.imageUrl && <img src={attributes.imageUrl} alt="" />}
                </div>
            </>
        );
    },
    save() { return null; }, // Server-side render
});
```

### Importing in editor.js

```js
// resources/js/editor.js
import '../../modules/my-block/editor.jsx';
```

Vite compiles it. WordPress packages are externalized (loaded from WP globals).

### render.php

```php
<?php
$wrapper = get_block_wrapper_attributes();
?>
<div <?php echo $wrapper; ?>>
    <?php if (!empty($attributes['imageUrl'])) : ?>
        <img src="<?php echo esc_url($attributes['imageUrl']); ?>" alt="">
    <?php endif; ?>
</div>
```

---

## CPT-Related Blocks

Blocks tied to a CPT live inside the parent module:

```
modules/testimonials/
├── module.php
├── acf-json/
└── blocks/
    └── testimonial-card/
        ├── block.json
        ├── render.php
        └── style.css
```

`CptModule` auto-registers blocks from `blocks/` subdirectories via `register_block_type()` using `block.json`.

---

## References

- [PHP-Only Block Registration — WP 7.0 Dev Note](https://make.wordpress.org/core/2026/03/03/php-only-block-registration/)
- [Trac #64639 — autoRegister](https://core.trac.wordpress.org/ticket/64639)
- [Gutenberg PR #71794 — Implementation](https://github.com/WordPress/gutenberg/pull/71794)
- [Block Supports Reference](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/)
- [Block Attributes Reference](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-attributes/)
- [Registration of a Block](https://developer.wordpress.org/block-editor/getting-started/fundamentals/registration-of-a-block/)
