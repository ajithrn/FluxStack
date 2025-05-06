<?php
/**
 * Portfolio Module
 *
 * @package FluxStack
 */

class FS_Portfolio {
    /**
     * Initialize the module
     */
    public static function init() {
        // Register post type and taxonomy
        add_action('init', [__CLASS__, 'register_post_type']);
        add_action('init', [__CLASS__, 'register_taxonomy']);
        
        // ACF fields registration
        add_action('acf/init', [__CLASS__, 'register_acf_fields']);
        
        // Admin columns
        add_filter('manage_portfolio_posts_columns', [__CLASS__, 'add_admin_columns']);
        add_action('manage_portfolio_posts_custom_column', [__CLASS__, 'render_admin_columns'], 10, 2);
        add_filter('manage_edit-portfolio_sortable_columns', [__CLASS__, 'register_sortable_columns']);
        
        // Admin sorting
        add_action('pre_get_posts', [__CLASS__, 'sort_admin_columns']);
        
        // ACF JSON save/load points
        add_filter('acf/settings/save_json/type=portfolio', [__CLASS__, 'set_acf_json_save_point']);
        add_filter('acf/settings/load_json', [__CLASS__, 'set_acf_json_load_point']);
    }
    
    /**
     * Register the portfolio post type
     */
    public static function register_post_type() {
        $labels = [
            'name'               => _x('Portfolio', 'post type general name', 'fluxstack'),
            'singular_name'      => _x('Portfolio Item', 'post type singular name', 'fluxstack'),
            'menu_name'          => _x('Portfolio', 'admin menu', 'fluxstack'),
            'name_admin_bar'     => _x('Portfolio', 'add new on admin bar', 'fluxstack'),
            'add_new'            => _x('Add New', 'portfolio item', 'fluxstack'),
            'add_new_item'       => __('Add New Portfolio', 'fluxstack'),
            'new_item'           => __('New Portfolio Item', 'fluxstack'),
            'edit_item'          => __('Edit Portfolio Item', 'fluxstack'),
            'view_item'          => __('View Portfolio Item', 'fluxstack'),
            'all_items'          => __('All Portfolio', 'fluxstack'),
            'search_items'       => __('Search Portfolio Items', 'fluxstack'),
            'parent_item_colon'  => __('Parent Portfolio Items:', 'fluxstack'),
            'not_found'          => __('No portfolio items found.', 'fluxstack'),
            'not_found_in_trash' => __('No portfolio items found in Trash.', 'fluxstack')
        ];
        
        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'portfolio'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-portfolio',
            'supports'           => ['title', 'thumbnail', 'excerpt', 'revisions', 'page-attributes'],
            'show_in_rest'       => false,
        ];
        
        register_post_type('portfolio', $args);
    }
    
    /**
     * Register the portfolio type taxonomy
     */
    public static function register_taxonomy() {
        $labels = [
            'name'              => _x('Portfolio Types', 'taxonomy general name', 'fluxstack'),
            'singular_name'     => _x('Portfolio Type', 'taxonomy singular name', 'fluxstack'),
            'search_items'      => __('Search Portfolio Types', 'fluxstack'),
            'all_items'         => __('All Portfolio Types', 'fluxstack'),
            'parent_item'       => __('Parent Portfolio Type', 'fluxstack'),
            'parent_item_colon' => __('Parent Portfolio Type:', 'fluxstack'),
            'edit_item'         => __('Edit Portfolio Type', 'fluxstack'),
            'update_item'       => __('Update Portfolio Type', 'fluxstack'),
            'add_new_item'      => __('Add New Portfolio Type', 'fluxstack'),
            'new_item_name'     => __('New Portfolio Type Name', 'fluxstack'),
            'menu_name'         => __('Portfolio Types', 'fluxstack'),
        ];
        
        $args = [
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'portfolio-type'],
            'show_in_rest'      => true,
        ];
        
        register_taxonomy('portfolio_type', ['portfolio'], $args);
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
        $new_columns['client'] = __('Client', 'fluxstack');
        $new_columns['year'] = __('Year', 'fluxstack');
        $new_columns['portfolio_type'] = __('Type', 'fluxstack');
        
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
                    echo '<div style="width:60px;height:60px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;"><span class="dashicons dashicons-format-image"></span></div>';
                }
                break;
                
            case 'client':
                $client = get_field('basic_info_client', $post_id);
                $website = get_field('basic_info_website', $post_id);
                
                if ($client) {
                    echo '<strong>' . esc_html($client) . '</strong>';
                    
                    if ($website) {
                        echo '<br><a href="' . esc_url($website) . '" target="_blank" class="portfolio-website-link">';
                        echo '<small>' . esc_url($website) . '</small>';
                        echo '</a>';
                    }
                } else {
                    echo '—';
                }
                break;
                
            case 'year':
                $year = get_field('basic_info_year', $post_id);
                echo $year ? esc_html($year) : '—';
                break;
                
            case 'portfolio_type':
                $terms = get_the_terms($post_id, 'portfolio_type');
                if (!empty($terms) && !is_wp_error($terms)) {
                    $term_links = [];
                    foreach ($terms as $term) {
                        $term_links[] = sprintf(
                            '<a href="%s">%s</a>',
                            esc_url(add_query_arg(['post_type' => 'portfolio', 'portfolio_type' => $term->slug], 'edit.php')),
                            esc_html($term->name)
                        );
                    }
                    echo implode(', ', $term_links);
                } else {
                    echo '—';
                }
                break;
        }
    }
    
    /**
     * Register sortable columns
     */
    public static function register_sortable_columns($columns) {
        $columns['client'] = 'client';
        $columns['year'] = 'year';
        $columns['portfolio_type'] = 'portfolio_type';
        return $columns;
    }
    
    /**
     * Handle admin column sorting
     */
    public static function sort_admin_columns($query) {
        if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'portfolio') {
            return;
        }
        
        $orderby = $query->get('orderby');
        
        switch ($orderby) {
            case 'client':
                $query->set('meta_key', 'basic_info_client');
                $query->set('orderby', 'meta_value');
                break;
                
            case 'year':
                $query->set('meta_key', 'basic_info_year');
                $query->set('orderby', 'meta_value_num');
                break;
                
            case 'portfolio_type':
                $query->set('tax_query', [
                    [
                        'taxonomy' => 'portfolio_type',
                        'field'    => 'term_id',
                        'terms'    => get_terms(['taxonomy' => 'portfolio_type', 'fields' => 'ids']),
                    ],
                ]);
                break;
        }
    }
    
    /**
     * Set ACF JSON save point
     */
    public static function set_acf_json_save_point($path) {
        return get_stylesheet_directory() . '/modules/portfolio/acf-json';
    }
    
    /**
     * Set ACF JSON load point
     */
    public static function set_acf_json_load_point($paths) {
        $paths[] = get_stylesheet_directory() . '/modules/portfolio/acf-json';
        return $paths;
    }
    
    /**
     * Get portfolio items
     * 
     * @param array $args Query arguments
     * @return WP_Query
     */
    public static function get_portfolio_items($args = []) {
        $defaults = [
            'post_type'      => 'portfolio',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ];
        
        $args = wp_parse_args($args, $defaults);
        return new WP_Query($args);
    }
    
    /**
     * Get portfolio items by type
     * 
     * @param string|array $type Portfolio type slug(s)
     * @param int $limit Number of items to return
     * @return WP_Query
     */
    public static function get_portfolio_by_type($type, $limit = -1) {
        return self::get_portfolio_items([
            'tax_query' => [
                [
                    'taxonomy' => 'portfolio_type',
                    'field'    => 'slug',
                    'terms'    => $type,
                ],
            ],
            'posts_per_page' => $limit,
        ]);
    }
    
    /**
     * Get portfolio items by year
     * 
     * @param int $year Year to filter by
     * @param int $limit Number of items to return
     * @return WP_Query
     */
    public static function get_portfolio_by_year($year, $limit = -1) {
        return self::get_portfolio_items([
            'meta_query' => [
                [
                    'key'   => 'basic_info_year',
                    'value' => $year,
                ],
            ],
            'posts_per_page' => $limit,
        ]);
    }
}

// Initialize the module
FS_Portfolio::init();
