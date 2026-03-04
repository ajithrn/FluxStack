<?php
/**
 * Testimonials Module
 *
 * Manages the 'testimonial' custom post type.
 *
 * @package FluxStack
 */

class FS_Testimonials extends FS_Base_CPT_Module {
    protected static function get_module_dir() {
        return 'testimonials';
    }

    protected static function get_acf_group_id() {
        return 'group_testimonials_meta_fields';
    }

    protected static function get_post_type_config() {
        return array(
            'post_type'     => 'testimonial',
            'slug'          => 'testimonial',
            'menu_icon'     => 'dashicons-format-quote',
            'menu_position' => 20,
            'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'labels'        => array(
                'name'               => _x('Testimonials', 'Post Type General Name', 'fluxstack'),
                'singular_name'      => _x('Testimonial', 'Post Type Singular Name', 'fluxstack'),
                'menu_name'          => __('Testimonials', 'fluxstack'),
                'name_admin_bar'     => __('Testimonial', 'fluxstack'),
                'all_items'          => __('All Testimonials', 'fluxstack'),
                'add_new_item'       => __('Add New Testimonial', 'fluxstack'),
                'add_new'            => __('Add New', 'fluxstack'),
                'new_item'           => __('New Testimonial', 'fluxstack'),
                'edit_item'          => __('Edit Testimonial', 'fluxstack'),
                'update_item'        => __('Update Testimonial', 'fluxstack'),
                'view_item'          => __('View Testimonial', 'fluxstack'),
                'search_items'       => __('Search Testimonial', 'fluxstack'),
                'not_found'          => __('Not found', 'fluxstack'),
                'not_found_in_trash' => __('Not found in Trash', 'fluxstack'),
            ),
            'args' => array(
                'capability_type' => 'page',
            ),
        );
    }

    protected static function get_taxonomy_config() {
        return array(
            array(
                'taxonomy' => 'testimonial_category',
                'slug'     => 'testimonial-category',
                'labels'   => array(
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
                ),
            ),
        );
    }

    protected static function get_custom_columns() {
        return array(
            'review_title' => __('Review Title', 'fluxstack'),
            'rating'       => __('Rating', 'fluxstack'),
            'thumbnail'    => __('Profile Image', 'fluxstack'),
        );
    }

    protected static function get_sortable_columns() {
        return array(
            'rating' => 'rating',
        );
    }

    protected static function register_hooks() {
        // Dynamic category field choices
        add_filter('acf/load_field/name=gb_testimonial_category', array(__CLASS__, 'acf_load_testimonial_categories_field_choices'));
    }

    public static function render_column($column, $post_id) {
        switch ($column) {
            case 'review_title':
                $summary = get_field('testimonial_summary', $post_id);
                echo $summary ? esc_html($summary) : '—';
                break;
            case 'rating':
                $rating = get_field('testimonial_rating', $post_id);
                if ($rating !== false && $rating !== null) {
                    echo str_repeat('★', intval($rating)) . str_repeat('☆', 5 - intval($rating));
                } else {
                    echo '—';
                }
                break;
            default:
                parent::render_column($column, $post_id);
                break;
        }
    }

    public static function handle_column_sorting($query) {
        if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'testimonial') {
            return;
        }

        if ($query->get('orderby') === 'rating') {
            $query->set('meta_key', 'testimonial_rating');
            $query->set('orderby', 'meta_value_num');
        }
    }

    /**
     * Load testimonial category choices for ACF field
     */
    public static function acf_load_testimonial_categories_field_choices($field) {
        $field['choices'] = self::load_testimonial_categories();
        return $field;
    }

    private static function load_testimonial_categories() {
        $options = array(0 => __('All', 'fluxstack'));

        $categories = get_terms(array(
            'taxonomy'   => 'testimonial_category',
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
     * Get testimonials
     *
     * @param array $args Query arguments.
     * @return WP_Query
     */
    public static function get_testimonials($args = array()) {
        return self::get_items($args);
    }
}
