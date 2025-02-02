<?php
class FS_Image_Gallery {
    const ACF_GROUP_ID = 'group_5f1a8d9e3c1b5';
    private static $acf_json_path;

    public static function init() {
        self::$acf_json_path = get_stylesheet_directory() . '/modules/image-gallery/acf-json';
        
        add_action('init', array(__CLASS__, 'register_post_type'), 0);
        add_action('init', array(__CLASS__, 'register_taxonomy'), 0);
        add_filter('acf/settings/load_json', array(__CLASS__, 'add_acf_json_load_point'));
        add_action('acf/update_field_group', array(__CLASS__, 'update_field_group'), 1, 1);

        //Update category field
        add_filter('acf/load_field/name=gb_image_gallery_category', array(__CLASS__, 'acf_load_image_gallery_categories_field_choices'));
        
        // Add admin columns
        add_filter('manage_image-gallery_posts_columns', array(__CLASS__, 'set_custom_columns'));
        add_action('manage_image-gallery_posts_custom_column', array(__CLASS__, 'custom_column_content'), 10, 2);
    }

    public static function register_taxonomy() {
        $labels = array(
            'name'              => _x('Gallery Categories', 'taxonomy general name', 'fluxstack'),
            'singular_name'     => _x('Gallery Category', 'taxonomy singular name', 'fluxstack'),
            'search_items'      => __('Search Gallery Categories', 'fluxstack'),
            'all_items'         => __('All Gallery Categories', 'fluxstack'),
            'parent_item'       => __('Parent Gallery Category', 'fluxstack'),
            'parent_item_colon' => __('Parent Gallery Category:', 'fluxstack'),
            'edit_item'         => __('Edit Gallery Category', 'fluxstack'),
            'update_item'       => __('Update Gallery Category', 'fluxstack'),
            'add_new_item'      => __('Add New Gallery Category', 'fluxstack'),
            'new_item_name'     => __('New Gallery Category Name', 'fluxstack'),
            'menu_name'         => __('Gallery Categories', 'fluxstack'),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'          => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_rest'      => false,
            'rewrite'           => array('slug' => 'gallery-category'),
        );

        register_taxonomy('gallery-category', array('image-gallery'), $args);
    }

    public static function set_custom_columns($columns) {
        $new_columns = array();
        foreach($columns as $key => $value) {
            if($key === 'date') {
                $new_columns[$key] = $value;
                $new_columns['thumbnail'] = __('Featured Image', 'fluxstack');
            } else {
                $new_columns[$key] = $value;
            }
        }
        return $new_columns;
    }

    public static function custom_column_content($column, $post_id) {
        if($column === 'thumbnail') {
            if(has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, array(50, 50));
            }
        }
    }

    public static function register_post_type() {
        $labels = array(
            'name'                => _x('Image Galleries', 'Post Type General Name', 'fluxstack'),
            'singular_name'       => _x('Image Gallery', 'Post Type Singular Name', 'fluxstack'),
            'menu_name'           => __('Image Gallery', 'fluxstack'),
            'name_admin_bar'      => __('Image Gallery', 'fluxstack'),
            'parent_item_colon'   => __('Parent Gallery:', 'fluxstack'),
            'all_items'           => __('All Galleries', 'fluxstack'),
            'add_new_item'        => __('Add New Gallery', 'fluxstack'),
            'add_new'             => __('Add New', 'fluxstack'),
            'new_item'            => __('New Gallery', 'fluxstack'),
            'edit_item'           => __('Edit Gallery', 'fluxstack'),
            'update_item'         => __('Update Gallery', 'fluxstack'),
            'view_item'           => __('View Gallery', 'fluxstack'),
            'search_items'        => __('Search Gallery', 'fluxstack'),
            'not_found'           => __('Not found', 'fluxstack'),
            'not_found_in_trash'  => __('Not found in Trash', 'fluxstack'),
        );
        $args = array(
            'label'               => __('image-gallery', 'fluxstack'),
            'description'         => __('Image Galleries ', 'fluxstack'),
            'labels'              => $labels,
            'supports'            => array('title', 'thumbnail'),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 25,
            'menu_icon'           => 'dashicons-camera',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => true,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
        );
        register_post_type('image-gallery', $args);
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

    private static function load_image_gallery_categories() {
        $options = array(
            0 => __('All', 'fluxstack')
        );
        
        $categories = get_terms(array(
            'taxonomy' => 'gallery-category',
            'hide_empty' => false,
        ));

        if (!empty($categories) && !is_wp_error($categories)) {
            foreach ($categories as $category) {
                $options[$category->term_id] = $category->name;
            }
        }

        return $options;
    }

    public static function acf_load_image_gallery_categories_field_choices($field) {
        $field['choices'] = self::load_image_gallery_categories();
        return $field;
    }
}

// Initialize the image gallery module
FS_Image_Gallery::init();
