# FluxStack WordPress Theme

FluxStack is a powerful WordPress child theme built on top of the Bricks Builder theme. It provides native block support and advanced ACF integration for creating dynamic, modern websites.

## Requirements

- WordPress 5.0+
- [Bricks Builder](https://bricksbuilder.io/) (Parent Theme)
- [Advanced Custom Fields PRO](https://www.advancedcustomfields.com/pro/)

## Installation

1. Install and activate the Bricks Builder theme
2. Install and activate Advanced Custom Fields PRO plugin
3. Upload the FluxStack theme to your `/wp-content/themes/` directory
4. Activate the FluxStack theme through the WordPress admin panel

## Directory Structure

```
fluxstack/
├── acf-json/                # ACF field group JSON files
├── modules/                 # Theme modules
│   ├── bricks/             # Bricks Builder customizations
│   │   └── bricks.php      # FS_Bricks class
│   ├── dynamic-snippets/   # Dynamic code snippets
│   │   ├── global/         # Global snippets
│   │   └── acf-json/       # Snippet field configurations
│   ├── image-gallery/      # Image gallery module
│   │   ├── acf-json/       # Gallery field configurations
│   │   └── image-gallery.php # FS_Image_Gallery class
│   ├── testimonials/       # Testimonials module
│   │   ├── acf-json/       # Testimonial field configurations
│   │   └── testimonials.php # FS_Testimonials class
│   ├── theme-options/      # Theme settings
│   │   ├── acf-json/       # Theme options field configurations
│   │   └── theme-options.php # FS_Theme_Options class
│   ├── white-label/        # White label module
│   │   ├── assets/        # Module assets
│   │   │   ├── scss/     # SCSS source files
│   │   │   │   ├── _variables.scss    # Variables and mixins
│   │   │   │   ├── _wp-admin.scss    # Admin interface styles
│   │   │   │   ├── _login.scss       # Login page styles
│   │   │   │   ├── _page-builder.scss # Builder customization
│   │   │   │   ├── _acf.scss         # ACF UI enhancement
│   │   │   │   └── admin.scss        # Main entry point
│   │   │   └── css/      # Compiled CSS
│   │   └── white-label.php # FS_White_Label class
│   └── utility-functions/  # Helper functions
│       └── utility-functions.php # FS_Utils class
├── native-blocks/          # Custom Gutenberg blocks
├── style.css              # Theme stylesheet
├── functions.php          # Theme functions
└── README.md             # This documentation
```

## Module Architecture

The theme follows a modular architecture with consistent patterns:

### Class-Based Structure
- All modules use the `FS_` prefix (e.g., `FS_Theme_Options`, `FS_Utils`)
- Each module has a static `init()` method for initialization
- Methods are organized within classes for better encapsulation
- Function wrappers provided for backward compatibility

### ACF Integration
- ACF field configurations stored in JSON format
- Each module maintains its own ACF JSON directory
- Automatic field synchronization support
- Organized field groups by functionality

### Module Features

1. **Bricks Module** (`FS_Bricks`)
   - Custom elements registration
   - Template management
   - Builder state detection
   - Settings customization

2. **Theme Options** (`FS_Theme_Options`)
   - Centralized options management with seamless interface
   - Site branding (logo)
   - Contact information management
   - Dynamic social media management with Font Awesome icons
   - Flexible platform selection and URL configuration

3. **Image Gallery** (`FS_Image_Gallery`)
   - Custom post type for galleries
   - Category taxonomy
   - Admin interface customizations
   - Image management features

4. **Testimonials** (`FS_Testimonials`)
   - Testimonial management system
   - Rating system
   - Category organization
   - Admin columns and sorting

5. **Dynamic Snippets** (`FS_Dynamic_Snippets`)
   - Reusable code snippets
   - Global snippet functions
   - Bricks Builder integration
   - Social media components

6. **Utility Functions** (`FS_Utils`)
   - Helper methods
   - Image handling
   - Date formatting
   - Post meta utilities

7. **White Label** (`FS_White_Label`)
   - Configurable via constants
   - Custom admin footer branding
   - WordPress core pattern removal
   - Version number removal
   - Modular SCSS architecture:
     * WordPress admin interface styling
     * Login page customization
     * Page builder interface tweaks
     * ACF fields and UI enhancements
     * Minified production output

## Development

### White Label Customization

The white label module uses a modular SCSS architecture for admin interface styling:

1. Navigate to the white label module:
   ```bash
   cd wp-content/themes/fluxstack/modules/white-label
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Edit SCSS files:
   - `assets/scss/_variables.scss`: Color schemes and variables
   - `assets/scss/_wp-admin.scss`: WordPress admin interface styles
   - `assets/scss/_login.scss`: Login page customization
   - `assets/scss/_page-builder.scss`: Page builder interface tweaks
   - `assets/scss/_acf.scss`: ACF fields and UI enhancements
   - `assets/scss/admin.scss`: Main entry point using modern @use syntax

Note: The white label module requires Node.js >= 18.0.0 for SCSS compilation.

4. Compile SCSS:
   - One-time build: `npm run build`
   - Watch mode: `npm run watch`

5. The compiled CSS will be available at:
   `assets/css/admin.min.css`

### Adding New Modules
1. Create a new directory under `modules/`
2. Follow the `FS_` class naming convention
3. Implement static `init()` method
4. Add initialization to `modules.php`

### ACF Fields
1. Create `acf-json` directory in module
2. Export field configurations as JSON
3. Register load/save points in module class
4. Follow existing field group naming patterns

### Coding Standards
- Follow WordPress coding standards
- Use class-based architecture
- Maintain backward compatibility
- Document all methods and hooks

## Features

- Seamless integration with Bricks Builder
- Custom Gutenberg blocks
- Advanced Custom Fields integration
- Modular architecture
- Dynamic snippets support
- Utility functions
- Theme customization options

## Author

**Ajith R N**
- Website: [ajithrn.com](https://ajithrn.com)
- Contact: [ajithrn.com/contact](https://ajithrn.com/contact)

## Version

Current version: 1.0.0

## License

This theme is licensed under the GPL v2 or later.

See [LICENSE](http://www.gnu.org/licenses/gpl-2.0.html) for more information.
