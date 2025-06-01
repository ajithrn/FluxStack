<?php
/**
 * Services Module
 *
 * @package FluxStack
 */

class FS_Services {
    /**
     * Initialize the module
     */
    public static function init() {
        // Register post type
        add_action('init', [__CLASS__, 'register_post_type']);
        
        // ACF fields registration
        add_action('acf/init', [__CLASS__, 'register_acf_fields']);
        
        // Admin columns
        add_filter('manage_service_posts_columns', [__CLASS__, 'add_admin_columns']);
        add_action('manage_service_posts_custom_column', [__CLASS__, 'render_admin_columns'], 10, 2);
        add_filter('manage_edit-service_sortable_columns', [__CLASS__, 'register_sortable_columns']);
        
        // Admin sorting
        add_action('pre_get_posts', [__CLASS__, 'sort_admin_columns']);
        
        // ACF JSON save/load points
        add_filter('acf/settings/save_json/type=service', [__CLASS__, 'set_acf_json_save_point']);
        add_filter('acf/settings/load_json', [__CLASS__, 'set_acf_json_load_point']);
    }
    
    /**
     * Register the services post type
     */
    public static function register_post_type() {
        $labels = [
            'name'               => _x('Services', 'post type general name', 'fluxstack'),
            'singular_name'      => _x('Service', 'post type singular name', 'fluxstack'),
            'menu_name'          => _x('Services', 'admin menu', 'fluxstack'),
            'name_admin_bar'     => _x('Services', 'add new on admin bar', 'fluxstack'),
            'add_new'            => _x('Add New', 'service', 'fluxstack'),
            'add_new_item'       => __('Add New Service', 'fluxstack'),
            'new_item'           => __('New Service', 'fluxstack'),
            'edit_item'          => __('Edit Service', 'fluxstack'),
            'view_item'          => __('View Service', 'fluxstack'),
            'all_items'          => __('All Services', 'fluxstack'),
            'search_items'       => __('Search Services', 'fluxstack'),
            'parent_item_colon'  => __('Parent Services:', 'fluxstack'),
            'not_found'          => __('No services found.', 'fluxstack'),
            'not_found_in_trash' => __('No services found in Trash.', 'fluxstack')
        ];
        
        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'services'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-admin-generic',
            'supports'           => ['title', 'thumbnail', 'revisions', 'page-attributes'],
            'show_in_rest'       => false,
        ];
        
        register_post_type('service', $args);
    }
    
    /**
     * Register ACF fields
     */
    public static function register_acf_fields() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }
        
        // Fields will be loaded from JSON
    }
    
    /**
     * Add custom admin columns
     */
    public static function add_admin_columns($columns) {
        $new_columns = [];
        
        // Add checkbox and thumbnail
        if (isset($columns['cb'])) {
            $new_columns['cb'] = $columns['cb'];
        }
        
        $new_columns['thumbnail'] = __('Thumbnail', 'fluxstack');
        
        // Add title
        if (isset($columns['title'])) {
            $new_columns['title'] = $columns['title'];
        }
        
        // Add custom columns
        $new_columns['menu_order'] = __('Order', 'fluxstack');
        
        // Add date
        if (isset($columns['date'])) {
            $new_columns['date'] = $columns['date'];
        }
        
        return $new_columns;
    }
    
    /**
     * Render custom admin columns
     */
    public static function render_admin_columns($column, $post_id) {
        switch ($column) {
            case 'thumbnail':
                if (has_post_thumbnail($post_id)) {
                    echo '<a href="' . esc_url(get_edit_post_link($post_id)) . '">';
                    echo get_the_post_thumbnail($post_id, [60, 60]);
                    echo '</a>';
                } else {
                    echo '<div style="width:60px;height:60px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;"><span class="dashicons dashicons-admin-generic"></span></div>';
                }
                break;
                
            case 'menu_order':
                echo get_post_field('menu_order', $post_id);
                break;
        }
    }
    
    /**
     * Register sortable columns
     */
    public static function register_sortable_columns($columns) {
        $columns['menu_order'] = 'menu_order';
        return $columns;
    }
    
    /**
     * Handle admin column sorting
     */
    public static function sort_admin_columns($query) {
        if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'service') {
            return;
        }
    }
    
    /**
     * Set ACF JSON save point
     */
    public static function set_acf_json_save_point($path) {
        return get_stylesheet_directory() . '/modules/services/acf-json';
    }
    
    /**
     * Set ACF JSON load point
     */
    public static function set_acf_json_load_point($paths) {
        $paths[] = get_stylesheet_directory() . '/modules/services/acf-json';
        return $paths;
    }
    
    /**
     * Get services
     * 
     * @param array $args Query arguments
     * @return WP_Query
     */
    public static function get_services($args = []) {
        $defaults = [
            'post_type'      => 'service',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ];
        
        $args = wp_parse_args($args, $defaults);
        return new WP_Query($args);
    }
}

// Initialize the module
FS_Services::init();
