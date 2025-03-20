# FluxStack Native Blocks

This directory contains custom blocks for the FluxStack theme. These blocks are built using the WordPress Block API and can be enabled or disabled through the FluxStack Settings page.

## Block Structure

Each block should be organized in its own directory with the following structure:

```
native-blocks/
  ├── block-name/
  │   ├── block.json       # Block metadata
  │   ├── block.php        # PHP render callback (if needed)
  │   ├── build.js         # Compiled JavaScript
  │   ├── editor.css       # Editor-specific styles
  │   ├── register.php     # Block registration
  │   └── style.css        # Frontend styles
  └── block-styles/        # Custom block styles
```

## Block Metadata

The `block.json` file should include the standard WordPress block metadata, plus FluxStack-specific metadata for dependencies:

```json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 3,
  "name": "fluxstack/block-name",
  "version": "1.0.0",
  "title": "Block Title",
  "category": "fluxstack",
  "icon": "block-icon",
  "description": "Block description",
  "supports": {
    "html": false,
    "anchor": true,
    "align": ["wide", "full"]
  },
  "textdomain": "fluxstack",
  "editorScript": "file:./build.js",
  "editorStyle": "file:./editor.css",
  "style": "file:./style.css",
  "fluxstack": {
    "moduleDependencies": ["module-name"],
    "blockDependencies": ["other-block-name"]
  }
}
```

## Dependencies

Blocks can have two types of dependencies:

1. **Module Dependencies**: Modules that must be enabled for the block to work
2. **Block Dependencies**: Other blocks that must be enabled for the block to work

These dependencies are defined in the `fluxstack` section of the `block.json` file.

## Block Registration

The `register.php` file should register the block using the WordPress Block API:

```php
<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Include block template
require_once dirname(__FILE__) . '/block.php';

/**
 * Register the block
 */
function fluxstack_register_block_name_block() {
    // Check if block editor is available.
    if (!function_exists('register_block_type')) {
        return;
    }

    // Get block name from directory name
    $block_name = basename(dirname(__FILE__));
    
    // Register assets using the helper function
    fluxstack_register_block_assets($block_name, array(
        'script_deps' => array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-block-editor'),
        'editor_deps' => array('wp-edit-blocks'),
        'style_deps'  => array(),
    ));

    // Register the block
    register_block_type(
        get_stylesheet_directory() . '/native-blocks/' . $block_name . '/block.json',
        array(
            'render_callback' => 'fluxstack_render_block_name_block_template',
        )
    );
}
add_action('init', 'fluxstack_register_block_name_block', 10);
```

## Block Styles

Custom block styles are stored in the `block-styles` directory. These styles can be enabled or disabled through the FluxStack Settings page.

## Enabling/Disabling Blocks

Blocks can be enabled or disabled through the FluxStack Settings page in the "Theme Blocks" section. When a block is disabled:

1. It won't be registered in the editor
2. Its assets won't be loaded
3. Any blocks that depend on it will also be disabled

## Creating a New Block

1. Create a new directory in `native-blocks/` with your block name
2. Copy the files from the `_template` directory
3. Update the files with your block's code
4. Add any dependencies to the `fluxstack` section of `block.json`
5. Register your block in `register.php`

## Content Module-Specific Blocks

If you're creating a block that depends on a specific content module (e.g., a Teams block that depends on the Teams module), make sure to add the module to the `moduleDependencies` array in the `block.json` file:

```json
"fluxstack": {
  "moduleDependencies": ["teams"],
  "blockDependencies": []
}
```

This will ensure that the block is automatically disabled if the required module is disabled.
