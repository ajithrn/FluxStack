# FluxStack Module Manager

The Module Manager allows site administrators to enable or disable specific FluxStack theme modules through a user-friendly interface.

## Features

- Toggle modules on/off through the WordPress admin interface
- Automatic dependency management
- Organized module grouping by functionality
- Responsive design for all screen sizes
- Clear visual indicators for module dependencies

## Usage

1. Navigate to **Appearance > FluxStack Settings** in the WordPress admin
2. Use the toggle switches to enable or disable modules
3. Modules with dependencies will automatically handle dependent modules
4. Click "Save Changes" to apply your settings
5. Use "Reset to Defaults" to restore the original configuration

## Module Dependencies

The Module Manager automatically handles dependencies between modules. If you try to disable a module that others depend on, the system will either:

1. Warn you and automatically enable the dependency
2. Disable dependent modules when you disable their dependencies

The utility-functions module is a core dependency for most other modules.

## Technical Details

### Settings Storage

Module settings are stored in the WordPress options table under the key `fluxstack_module_settings` as an array of module IDs and their enabled/disabled status:

```php
array(
    'bricks' => true,
    'dynamic-snippets' => true,
    'image-gallery' => true,
    // etc.
)
```

### Integration

The Module Manager integrates with the theme's module loading system by:

1. Loading first in the module initialization process
2. Checking each module's enabled status before loading
3. Respecting dependency relationships between modules
4. Providing helper functions for conditional template code

### Template Integration

When creating templates that use module functionality, you can check if a module is enabled:

```php
<?php if (function_exists('FS_Module_Manager::is_module_enabled') && FS_Module_Manager::is_module_enabled('teams')) : ?>
    <!-- Teams module content -->
<?php endif; ?>
```

Or check for specific functions:

```php
<?php if (function_exists('fs_get_team_members')) : ?>
    <!-- Team members display code -->
<?php endif; ?>
```

## Performance Impact

Enabling the Module Manager can improve site performance by:

1. Reducing unnecessary database queries from disabled modules
2. Preventing loading of CSS/JS assets from disabled modules
3. Simplifying the admin interface by hiding unused functionality

## Customization

Developers can extend the Module Manager by:

1. Adding new modules to the `$default_modules` array
2. Defining dependencies in the `$module_dependencies` array
3. Organizing modules into groups via the `$module_groups` array
4. Adding custom descriptions in the `$module_descriptions` array
