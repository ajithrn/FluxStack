# FluxStack Services Module

The Services module provides a comprehensive system for managing and displaying services offered by your organization or business.

## Features

- Custom post type for services with featured image support
- Intro text section for use in homepage widgets and listings
- Detailed content section for service descriptions
- Service features repeater field for highlighting key aspects
- Custom admin columns for better management
- Sortable admin interface with ordering options
- Helper functions for retrieving services

## Usage

### Adding a New Service

1. Navigate to **Services** in the WordPress admin menu
2. Click **Add New**
3. Enter the service title
4. Add a featured image that represents the service
5. Fill in the intro text for use in listings and homepage widgets
6. Add detailed content in the content section
7. Add service features using the repeater field
8. Set the menu order for controlling the display order
9. Publish the service

### Retrieving Services

The module provides helper functions for retrieving services:

```php
// Get all services
$services = FS_Services::get_services();

// Get services with custom arguments
$services = FS_Services::get_services([
    'posts_per_page' => 3,
    'orderby' => 'title',
    'order' => 'ASC'
]);

// Loop through services
if ($services->have_posts()) {
    while ($services->have_posts()) {
        $services->the_post();
        
        // Get service data
        $title = get_the_title();
        $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
        $intro_text = get_field('intro_section')['intro_text'];
        $content = get_field('content_section')['content'];
        
        // Display service
        // Your display code here
    }
    wp_reset_postdata();
}
```

## Field Structure

The Services module includes the following ACF fields:

### Intro Section
- **Intro Text**: A short introduction text for use in listings and homepage widgets

### Content Section
- **Service Content**: The main content for the service using the WYSIWYG editor

### Additional Information
- **Service Features**: A repeater field for adding key features of the service
  - **Title**: The feature title
  - **Description**: A description of the feature

## Template Integration

When creating templates that use the Services module, you can check if the module is enabled:

```php
<?php if (function_exists('FS_Module_Manager::is_module_enabled') && FS_Module_Manager::is_module_enabled('services')) : ?>
    <!-- Services module content -->
<?php endif; ?>
```

## Customization

### Adding Custom Fields

You can add custom fields to the Services module by:

1. Exporting the field group from ACF
2. Modifying the JSON file in the `acf-json` directory
3. Importing the modified field group back into ACF

### Modifying the Admin Interface

You can customize the admin columns and filters by extending the `FS_Services` class:

```php
// Add a custom admin column
add_filter('manage_service_posts_columns', function($columns) {
    $columns['custom_field'] = __('Custom Field', 'fluxstack');
    return $columns;
});

// Render the custom column content
add_action('manage_service_posts_custom_column', function($column, $post_id) {
    if ($column === 'custom_field') {
        echo get_field('custom_field', $post_id);
    }
}, 10, 2);
```

## Integration with Other Modules

The Services module can be integrated with other FluxStack modules:

- **Bricks Builder**: Create custom elements for displaying services
- **Dynamic Snippets**: Create reusable code snippets for service displays
- **Theme Options**: Add service-specific theme options

## Performance Considerations

For optimal performance:

1. Use appropriate image sizes for featured images
2. Limit the number of service features to improve load times
3. Consider using pagination when displaying multiple services
4. Cache service queries when appropriate
