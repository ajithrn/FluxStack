# FluxStack Block Template

This directory contains template files for creating new custom blocks for the FluxStack theme.

## How to Create a New Block

1. **Create a new directory** for your block in the `native-blocks` folder:
   ```
   wp-content/themes/fluxstack/native-blocks/your-block-name/
   ```

2. **Copy the template files** from this directory to your new block directory:
   - `block.json` - Block metadata
   - `block.php` - Block rendering template
   - `register.php` - Block registration
   - `index.js` - JavaScript source (to be compiled to build.js)
   - `style.css` - Frontend styles
   - `editor.css` - Editor-specific styles

3. **Update the files** with your block-specific code:
   - Replace all instances of `block-name` with your actual block name
   - Replace all instances of `Block Name` with your block's display name
   - Update the block description, icon, and other metadata
   - Implement your block's functionality in the JavaScript file
   - Add your block's styles to the CSS files
   - Update the render callback in the PHP file

4. **Compile your JavaScript** to `build.js` using your preferred build tool.

5. **That's it!** The block will be automatically discovered and registered by the system.

## File Purposes

- **block.json**: Defines block metadata for the WordPress block editor
- **block.php**: Contains the PHP rendering function for server-side rendering
- **register.php**: Handles block registration with WordPress
- **index.js**: Source JavaScript file for the block editor interface
- **build.js**: Compiled JavaScript (from index.js)
- **style.css**: Frontend styles applied to the block on the site
- **editor.css**: Styles applied only in the editor

## Naming Conventions

- Function names should follow the pattern: `fluxstack_render_{block-name}_block_template`
- CSS classes should follow the pattern: `fluxstack-{block-name}`
- Block names should follow the pattern: `fluxstack/{block-name}`

## Tips

- Use the helper function `fluxstack_register_block_assets()` to register your block's assets
- Keep your block's functionality focused on a single purpose
- Use InnerBlocks when you need to allow content within your block
- Test your block thoroughly in both the editor and the frontend
