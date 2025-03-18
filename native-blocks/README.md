# FluxStack Native Blocks

This directory contains custom blocks for the WordPress block editor (Gutenberg) used in the FluxStack theme.

## Overview

The FluxStack theme includes a system for creating and managing custom blocks. The system is designed to be:

- **Modular**: Each block is contained in its own directory
- **Extensible**: New blocks can be added easily
- **Maintainable**: Common functionality is centralized
- **Discoverable**: Blocks are automatically registered

## Directory Structure

```
native-blocks/
├── _template/                # Template files for creating new blocks
├── columns-25-75/            # Example block: 25/75 column layout
│   ├── block.json            # Block metadata
│   ├── block.php             # Block rendering template
│   ├── build.js              # Compiled JavaScript
│   ├── editor.css            # Editor-specific styles
│   ├── register.php          # Block registration
│   └── style.css             # Frontend styles
├── [other-blocks]/           # Additional blocks follow the same pattern
└── native-blocks.php         # Main loader file
```

## Adding a New Block

1. Create a new directory in the `native-blocks` folder for your block
2. Copy the template files from the `_template` directory
3. Customize the files for your specific block
4. The block will be automatically discovered and registered

See the [_template/README.md](./_template/README.md) file for detailed instructions.

## Available Blocks

### Columns 25/75

A two-column layout with a 25% width left column and a 75% width right column.

- **Usage**: Add the block to your content and fill the columns with your desired content
- **Options**: Supports wide and full alignment

## Helper Functions

The system provides several helper functions to make block development easier:

- `fluxstack_register_block_assets()`: Registers scripts and styles for a block
- `fluxstack_load_blocks()`: Auto-discovers and loads all blocks
- `fluxstack_block_category()`: Adds a custom block category

## Development Workflow

1. Create a new block using the template
2. Develop your block's functionality
3. Compile your JavaScript (if needed)
4. Test your block in the editor and on the frontend

## Best Practices

- Keep blocks focused on a single purpose
- Follow the naming conventions
- Use the helper functions for consistency
- Document your block's usage and options
- Test thoroughly in both the editor and frontend
