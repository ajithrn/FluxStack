# FluxStack WordPress Theme Documentation

FluxStack is a powerful WordPress child theme built on top of the Bricks Builder theme. It provides native block support and advanced ACF integration for creating dynamic, modern websites.

## Table of Contents

1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Directory Structure](#directory-structure)
4. [Module Architecture](#module-architecture)
5. [Module Features](#module-features)
6. [Native Blocks](#native-blocks)
7. [Development](#development)
8. [Theme Customization](#theme-customization)
9. [Frequently Asked Questions](#frequently-asked-questions)
10. [Changelog](#changelog)
11. [Support](#support)

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
├── modules/                 # Theme modules
│   ├── bricks/             # Bricks Builder customizations
│   │   └── bricks.php      # FS_Bricks class
│   ├── dynamic-snippets/   # Dynamic code snippets
│   │   ├── global/         # Global snippets
│   │   └── dynamic-snippets.php # FS_Dynamic_Snippets class
│   ├── image-gallery/      # Image gallery module
│   │   ├── acf-json/       # Gallery field configurations
│   │   └── image-gallery.php # FS_Image_Gallery class
│   ├── module-manager/     # Module manager for enabling/disabling modules
│   │   ├── assets/         # Module assets
│   │   │   ├── css/        # CSS styles
│   │   │   └── js/         # JavaScript files
│   │   └── module-manager.php # FS_Module_Manager class
│   ├── news-archives/      # News archives by year
│   │   └── news-archives.php # FS_News_Archives class
│   ├── portfolio/          # Portfolio management
│   │   ├── acf-json/       # Portfolio field configurations
│   │   └── portfolio.php   # FS_Portfolio class
│   ├── publications/       # Publications management
│   │   ├── acf-json/       # Publication field configurations
│   │   └── publications.php # FS_Publications class
│   ├── services/           # Services management
│   │   ├── acf-json/       # Service field configurations
│   │   └── services.php    # FS_Services class
│   ├── teams/              # Team members management
│   │   ├── acf-json/       # Team field configurations
│   │   └── teams.php       # FS_Teams class
│   ├── testimonials/       # Testimonials module
│   │   ├── acf-json/       # Testimonial field configurations
│   │   └── testimonials.php # FS_Testimonials class
│   ├── theme-options/      # Theme settings
│   │   ├── acf-json/       # Theme options field configurations
│   │   └── theme-options.php # FS_Theme_Options class
│   ├── utility-functions/  # Helper functions
│   │   └── utility-functions.php # FS_Utils class
│   └── white-label/        # White label module
│       ├── assets/         # Module assets
│       │   ├── scss/       # SCSS source files
│       │   └── css/        # Compiled CSS
│       └── white-label.php # FS_White_Label class
├── native-blocks/          # Custom Gutenberg blocks
│   ├── _template/          # Template for creating new blocks
│   ├── block-styles/       # Custom block styles
│   ├── columns-25-75/      # 25/75 column layout block
│   └── native-blocks.php   # Block loader
├── style.css               # Theme stylesheet
├── functions.php           # Theme functions
└── README.md               # Basic theme information
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

## Module Features

### 1. Bricks Module (`FS_Bricks`)
   - Custom elements registration
   - Template management
   - Builder state detection
   - Settings customization

### 2. Theme Options (`FS_Theme_Options`)
   - Centralized options management with seamless interface
   - Site branding (logo)
   - Contact information management
   - Dynamic social media management with Font Awesome icons
   - Flexible platform selection and URL configuration

### 3. Image Gallery (`FS_Image_Gallery`)
   - Custom post type for galleries
   - Category taxonomy
   - Admin interface customizations
   - Image management features

### 4. Testimonials (`FS_Testimonials`)
   - Testimonial management system
   - Rating system
   - Category organization
   - Admin columns and sorting

### 5. Dynamic Snippets (`FS_Dynamic_Snippets`)
   - Reusable code snippets
   - Global snippet functions
   - Bricks Builder integration
   - Social media components

### 6. Utility Functions (`FS_Utils`)
   - Helper methods
   - Image handling
   - Date formatting
   - Post meta utilities

### 7. White Label (`FS_White_Label`)
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

### 8. Teams (`FS_Teams`)
   - Team member management system
   - Custom post type for team members
   - Category taxonomy for team organization
   - Custom admin columns for position, email, phone
   - Profile image support
   - ACF integration for team member details
   - Seamless editing mode

### 9. Publications (`FS_Publications`)
   - Publication management system
   - Custom post type for publications
   - Publication type taxonomy (newsletters, handbooks, reports, etc.)
   - Date-based organization
   - PDF file attachment support
   - Admin filtering by publication type
   - Custom admin columns for publication type, date, and file
   - Helper functions for retrieving publications by type or year

### 10. News Archives (`FS_News_Archives`)
   - Year-based news organization
   - Automatic categorization of posts by year
   - Custom taxonomy for year-based archives
   - Admin filtering by year
   - Widget for displaying year-based archives
   - Helper functions for retrieving posts by year

### 11. Portfolio (`FS_Portfolio`)
   - Portfolio project management system
   - Custom post type for portfolio items
   - Portfolio type taxonomy for project categorization
   - Client, location, year, and website metadata
   - Project features and key details repeater fields
   - Image gallery support for project showcases
   - Custom admin columns for client, year, and type
   - Sortable admin interface with filtering options
   - Helper functions for retrieving portfolio items by type or year

### 12. Services (`FS_Services`)
   - Services management system
   - Custom post type for services
   - Featured image support for service visuals
   - Intro text section for use in homepage widgets and listings
   - Detailed content section for service descriptions
   - Service features repeater field for highlighting key aspects
   - Custom admin columns for better management
   - Sortable admin interface with ordering options
   - Helper functions for retrieving services

### 13. Module Manager (`FS_Module_Manager`)
   - Enable/disable modules through admin interface
   - Automatic dependency management
   - Performance optimization by disabling unused modules
   - Organized module grouping by functionality
   - Responsive settings interface with toggle switches
   - Clear visual indicators for module dependencies

## Native Blocks

FluxStack includes a system for creating and managing custom blocks for the WordPress block editor (Gutenberg).

### Block System Features
- Modular: Each block is contained in its own directory
- Extensible: New blocks can be added easily
- Maintainable: Common functionality is centralized
- Discoverable: Blocks are automatically registered
- Configurable: Blocks can be enabled/disabled through the Module Manager

### Available Blocks

#### Columns 25/75
A two-column layout with a 25% width left column and a 75% width right column.
- **Usage**: Add the block to your content and fill the columns with your desired content
- **Options**: Supports wide and full alignment

### Creating New Blocks
1. Create a new directory in the `native-blocks` folder for your block
2. Copy the template files from the `_template` directory
3. Customize the files for your specific block
4. Add any dependencies in the `fluxstack` section of `block.json`
5. The block will be automatically discovered and registered

### Block Structure
Each block follows a consistent structure:
```
block-name/
├── block.json            # Block metadata
├── block.php             # Block rendering template
├── build.js              # Compiled JavaScript
├── editor.css            # Editor-specific styles
├── register.php          # Block registration
└── style.css             # Frontend styles
```

### Block Dependencies
Blocks can have dependencies on modules or other blocks. These dependencies are defined in the `block.json` file:

```json
"fluxstack": {
  "moduleDependencies": ["module-name"],
  "blockDependencies": ["other-block-name"]
}
```

When a dependency is disabled, the block will be automatically disabled as well.

### Enabling/Disabling Blocks
Blocks can be enabled or disabled through the FluxStack Settings page:

1. Navigate to **Appearance > FluxStack Settings** in the WordPress admin
2. Scroll down to the **Theme Blocks** section
3. Use the toggle switches to enable or disable specific blocks
4. The system will automatically handle dependencies between blocks and modules
5. Click "Save Changes" to apply your settings

Disabled blocks won't be registered in the editor, improving performance and reducing clutter.

### Content Module-Specific Blocks
If you're creating blocks that depend on specific content modules (e.g., a Teams block that depends on the Teams module), make sure to add the module to the `moduleDependencies` array in the `block.json` file. This ensures that the block is automatically disabled if the required module is disabled.

For more detailed information, see the [Native Blocks README](./native-blocks/README.md).

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

## Theme Customization

### Theme Options
The Theme Options module provides a centralized interface for customizing various aspects of the theme:

1. **General Settings**
   - Site logo
   - Favicon
   - Default featured image
   - Google Analytics ID

2. **Contact Information**
   - Address
   - Phone numbers
   - Email addresses
   - Business hours

3. **Social Media**
   - Platform selection
   - URL configuration
   - Icon customization

4. **Footer Settings**
   - Copyright text
   - Footer logo
   - Footer menu configuration
   - Footer widgets

### Bricks Builder Integration
FluxStack extends the Bricks Builder with custom elements and settings:

1. **Custom Elements**
   - Access through the Bricks Builder element panel
   - Drag and drop functionality
   - Customizable settings

2. **Template Management**
   - Create and manage templates
   - Assign templates to specific content types
   - Global templates for consistent design

## Frequently Asked Questions

### How do I add a new team member?
1. Navigate to Teams in the WordPress admin menu
2. Click "Add New"
3. Enter the team member's name as the title
4. Fill in the custom fields for position, email, phone, etc.
5. Add a featured image for the team member's profile photo
6. Assign to a team category if needed
7. Publish the team member

### How do I create a new publication?
1. Navigate to Publications in the WordPress admin menu
2. Click "Add New"
3. Enter the publication title
4. Select the publication type
5. Set the publication date
6. Upload the PDF file
7. Add a featured image for the publication thumbnail
8. Publish the publication

### How do I add a new portfolio project?
1. Navigate to Portfolio in the WordPress admin menu
2. Click "Add New"
3. Enter the project title
4. Fill in the basic information (client, location, year, website)
5. Add a detailed project description
6. Add project features and key details using the repeater fields
7. Upload images to the portfolio gallery
8. Add a featured image as the main project thumbnail
9. Assign to a portfolio type if needed
10. Publish the portfolio item

### How do I add a new service?
1. Navigate to Services in the WordPress admin menu
2. Click "Add New"
3. Enter the service title
4. Add a featured image that represents the service
5. Fill in the intro text for use in listings and homepage widgets
6. Add detailed content in the content section
7. Add service features using the repeater field
8. Set the menu order for controlling the display order
9. Publish the service

### How do I customize the admin interface?
The White Label module allows for customization of the WordPress admin interface:
1. Navigate to the white label module directory
2. Edit the SCSS files to customize colors, branding, etc.
3. Compile the SCSS files
4. The changes will be applied to the admin interface

### How do I enable or disable theme modules?
The Module Manager allows you to control which modules are active:
1. Navigate to **Appearance > FluxStack Settings** in the WordPress admin
2. Use the toggle switches to enable or disable specific modules
3. The system will automatically handle dependencies between modules
4. Click "Save Changes" to apply your settings
5. Disabled modules won't load, improving performance

## Changelog

The complete changelog is available in the [CHANGELOG.md](./CHANGELOG.md) file.

## Support

For support, please contact:

**Ajith R N**
- Website: [ajithrn.com](https://ajithrn.com)
- Contact: [ajithrn.com/contact](https://ajithrn.com/contact)

## License

This theme is licensed under the GPL v2 or later.

See [LICENSE](http://www.gnu.org/licenses/gpl-2.0.html) for more information.
