<?php
/**
 * Image Gallery Module
 *
 * Manages the 'image-gallery' custom post type.
 *
 * @package FluxStack
 */

class FS_Image_Gallery extends FS_Base_CPT_Module {
    protected static function get_module_dir() {
        return 'image-gallery';
    }

    protected static function get_acf_group_id() {
        return 'group_5f1a8d9e3c1b5';
    }

    protected static function get_post_type_config() {
        return array(
            'post_type'     => 'image-gallery',
            'slug'          => 'image-gallery',
            'menu_icon'     => 'dashicons-camera',
            'menu_position' => 25,
            'supports'      => array('title', 'thumbnail'),
            'labels'        => array(
                'name'               => _x('Image Galleries', 'Post Type General Name', 'fluxstack'),
                'singular_name'      => _x('Image Gallery', 'Post Type Singular Name', 'fluxstack'),
                'menu_name'          => __('Image Gallery', 'fluxstack'),
                'name_admin_bar'     => __('Image Gallery', 'fluxstack'),
                'parent_item_colon'  => __('Parent Gallery:', 'fluxstack'),
                'all_items'          => __('All Galleries', 'fluxstack'),
                'add_new_item'       => __('Add New Gallery', 'fluxstack'),
                'add_new'            => __('Add New', 'fluxstack'),
                'new_item'           => __('New Gallery', 'fluxstack'),
                'edit_item'          => __('Edit Gallery', 'fluxstack'),
                'update_item'        => __('Update Gallery', 'fluxstack'),
                'view_item'          => __('View Gallery', 'fluxstack'),
                'search_items'       => __('Search Gallery', 'fluxstack'),
                'not_found'          => __('Not found', 'fluxstack'),
                'not_found_in_trash' => __('Not found in Trash', 'fluxstack'),
            ),
            'args' => array(
                'capability_type'     => 'page',
                'exclude_from_search' => true,
            ),
        );
    }

    protected static function get_taxonomy_config() {
        return array(
            array(
                'taxonomy' => 'gallery-category',
                'slug'     => 'gallery-category',
                'labels'   => array(
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
                ),
                'args' => array(
                    // Use the hyphenated post type name for the taxonomy registration
                ),
            ),
        );
    }

    protected static function get_custom_columns() {
        return array(
            'thumbnail' => __('Featured Image', 'fluxstack'),
        );
    }

    protected static function register_hooks() {
        // Dynamic category field choices
        add_filter('acf/load_field/name=gb_image_gallery_category', array(__CLASS__, 'acf_load_image_gallery_categories_field_choices'));
    }

    /**
     * Load gallery category choices for ACF field
     */
    public static function acf_load_image_gallery_categories_field_choices($field) {
        $field['choices'] = self::load_image_gallery_categories();
        return $field;
    }

    private static function load_image_gallery_categories() {
        $options = array(0 => __('All', 'fluxstack'));

        $categories = get_terms(array(
            'taxonomy'   => 'gallery-category',
            'hide_empty' => false,
        ));

        if (!empty($categories) && !is_wp_error($categories)) {
            foreach ($categories as $category) {
                $options[$category->term_id] = $category->name;
            }
        }

        return $options;
    }

    /**
     * Get galleries
     *
     * @param array $args Query arguments.
     * @return WP_Query
     */
    public static function get_galleries($args = array()) {
        return self::get_items($args);
    }
}
