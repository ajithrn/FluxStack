<?php
class FS_Testimonials {
    const ACF_GROUP_ID = 'group_testimonials_meta_fields';
    private static $acf_json_path;

    public static function init() {
        self::$acf_json_path = get_stylesheet_directory() . '/modules/testimonials/acf-json';
        
        add_action('init', array(__CLASS__, 'register_post_type'), 0);
        add_action('init', array(__CLASS__, 'register_taxonomy'), 0);
        add_filter('acf/settings/load_json', array(__CLASS__, 'add_acf_json_load_point'));
        add_action('acf/update_field_group', array(__CLASS__, 'update_field_group'), 1, 1);
        
        // Update category field
        add_filter('acf/load_field/name=gb_testimonial_category', array(__CLASS__, 'acf_load_testimonial_categories_field_choices'));
        
        // Add admin columns
        add_filter('manage_testimonial_posts_columns', array(__CLASS__, 'set_custom_columns'));
        add_action('manage_testimonial_posts_custom_column', array(__CLASS__, 'custom_column_content'), 10, 2);
        add_filter('manage_edit-testimonial_sortable_columns', array(__CLASS__, 'set_sortable_columns'));
        
        // Handle custom sorting
        add_action('pre_get_posts', array(__CLASS__, 'sort_testimonials_by_rating'));
    }

    public static function set_custom_columns($columns) {
        $new_columns = array();
        foreach($columns as $key => $value) {
            if($key === 'title') {
                $new_columns[$key] = $value;
                $new_columns['review_title'] = __('Review Title', 'fluxstack');
                $new_columns['rating'] = __('Rating', 'fluxstack');
            } else if($key === 'date') {
                $new_columns[$key] = $value;
                $new_columns['thumbnail'] = __('Profile Image', 'fluxstack');
            } else {
                $new_columns[$key] = $value;
            }
        }
        return $new_columns;
    }

    public static function custom_column_content($column, $post_id) {
        switch($column) {
            case 'review_title':
                $summary = get_field('testimonial_summary', $post_id);
                if($summary) {
                    echo esc_html($summary);
                }
                break;
            case 'rating':
                $rating = get_field('testimonial_rating', $post_id);
                if($rating !== false) {
                    echo str_repeat('★', intval($rating)) . str_repeat('☆', 5 - intval($rating));
                }
                break;
            case 'thumbnail':
                if(has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, array(50, 50));
                }
                break;
        }
    }

    public static function set_sortable_columns($columns) {
        $columns['rating'] = 'rating';
        return $columns;
    }

    public static function sort_testimonials_by_rating($query) {
        if(!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'testimonial') {
            return;
        }

        $orderby = $query->get('orderby');
        if('rating' === $orderby) {
            $query->set('meta_key', 'testimonial_rating');
            $query->set('orderby', 'meta_value_num');
        }
    }

    public static function register_post_type() {
        $labels = array(
            'name'                => _x('Testimonials', 'Post Type General Name', 'fluxstack'),
            'singular_name'       => _x('Testimonial', 'Post Type Singular Name', 'fluxstack'),
            'menu_name'           => __('Testimonials', 'fluxstack'),
            'name_admin_bar'      => __('Testimonial', 'fluxstack'),
            'parent_item_colon'   => __('Parent Testimonial:', 'fluxstack'),
            'all_items'           => __('All Testimonials', 'fluxstack'),
            'add_new_item'        => __('Add New Testimonial', 'fluxstack'),
            'add_new'             => __('Add New', 'fluxstack'),
            'new_item'            => __('New Testimonial', 'fluxstack'),
            'edit_item'           => __('Edit Testimonial', 'fluxstack'),
            'update_item'         => __('Update Testimonial', 'fluxstack'),
            'view_item'           => __('View Testimonial', 'fluxstack'),
            'search_items'        => __('Search Testimonial', 'fluxstack'),
            'not_found'           => __('Not found', 'fluxstack'),
            'not_found_in_trash'  => __('Not found in Trash', 'fluxstack'),
        );
        $args = array(
            'label'               => __('testimonial', 'fluxstack'),
            'description'         => __('Testimonials', 'fluxstack'),
            'labels'              => $labels,
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 20,
            'menu_icon'           => 'dashicons-format-quote',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
            'show_in_rest'        => false,
            'rewrite'            => array('slug' => 'testimonial')
        );
        register_post_type('testimonial', $args);
    }

    public static function register_taxonomy() {
        $labels = array(
            'name'              => _x('Testimonial Categories', 'taxonomy general name', 'fluxstack'),
            'singular_name'     => _x('Testimonial Category', 'taxonomy singular name', 'fluxstack'),
            'search_items'      => __('Search Testimonial Categories', 'fluxstack'),
            'all_items'         => __('All Testimonial Categories', 'fluxstack'),
            'parent_item'       => __('Parent Testimonial Category', 'fluxstack'),
            'parent_item_colon' => __('Parent Testimonial Category:', 'fluxstack'),
            'edit_item'         => __('Edit Testimonial Category', 'fluxstack'),
            'update_item'       => __('Update Testimonial Category', 'fluxstack'),
            'add_new_item'      => __('Add New Testimonial Category', 'fluxstack'),
            'new_item_name'     => __('New Testimonial Category Name', 'fluxstack'),
            'menu_name'         => __('Testimonial Categories', 'fluxstack'),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'          => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_rest'      => false,
            'rewrite'           => array('slug' => 'testimonial-category'),
        );

        register_taxonomy('testimonial_category', array('testimonial'), $args);
    }

    public static function add_acf_json_load_point($paths) {
        $paths[] = self::$acf_json_path;
        return $paths;
    }

    public static function update_field_group($group) {
        if ($group['key'] === self::ACF_GROUP_ID) {
            add_filter('acf/settings/save_json', function() {
                return self::$acf_json_path;
            });
        }
    }

    private static function load_testimonial_categories() {
        $options = array(
            0 => __('All', 'fluxstack')
        );
        
        $categories = get_terms(array(
            'taxonomy' => 'testimonial_category',
            'hide_empty' => false,
        ));

        if (!empty($categories) && !is_wp_error($categories)) {
            foreach ($categories as $category) {
                $options[$category->term_id] = $category->name;
            }
        }

        return $options;
    }

    public static function acf_load_testimonial_categories_field_choices($field) {
        $field['choices'] = self::load_testimonial_categories();
        return $field;
    }
}

// Initialize the testimonials module
FS_Testimonials::init();
