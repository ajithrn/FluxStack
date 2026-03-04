<?php
/**
 * Services Module
 *
 * Manages the 'service' custom post type.
 *
 * @package FluxStack
 */

class FS_Services extends FS_Base_CPT_Module {
    protected static function get_module_dir() {
        return 'services';
    }

    protected static function get_acf_group_id() {
        return 'group_services_meta_fields';
    }

    protected static function get_post_type_config() {
        return array(
            'post_type'     => 'service',
            'slug'          => 'service',
            'menu_icon'     => 'dashicons-admin-generic',
            'menu_position' => 20,
            'supports'      => array('title', 'thumbnail', 'custom-fields', 'page-attributes'),
            'labels'        => array(
                'name'               => _x('Services', 'Post Type General Name', 'fluxstack'),
                'singular_name'      => _x('Service', 'Post Type Singular Name', 'fluxstack'),
                'menu_name'          => __('Services', 'fluxstack'),
                'name_admin_bar'     => __('Service', 'fluxstack'),
                'all_items'          => __('All Services', 'fluxstack'),
                'add_new_item'       => __('Add New Service', 'fluxstack'),
                'add_new'            => __('Add New', 'fluxstack'),
                'new_item'           => __('New Service', 'fluxstack'),
                'edit_item'          => __('Edit Service', 'fluxstack'),
                'update_item'        => __('Update Service', 'fluxstack'),
                'view_item'          => __('View Service', 'fluxstack'),
                'search_items'       => __('Search Service', 'fluxstack'),
                'not_found'          => __('Not found', 'fluxstack'),
                'not_found_in_trash' => __('Not found in Trash', 'fluxstack'),
            ),
        );
    }

    protected static function get_taxonomy_config() {
        return array(
            array(
                'taxonomy' => 'service_category',
                'slug'     => 'service-category',
                'labels'   => array(
                    'name'              => _x('Service Categories', 'taxonomy general name', 'fluxstack'),
                    'singular_name'     => _x('Service Category', 'taxonomy singular name', 'fluxstack'),
                    'search_items'      => __('Search Service Categories', 'fluxstack'),
                    'all_items'         => __('All Service Categories', 'fluxstack'),
                    'parent_item'       => __('Parent Service Category', 'fluxstack'),
                    'parent_item_colon' => __('Parent Service Category:', 'fluxstack'),
                    'edit_item'         => __('Edit Service Category', 'fluxstack'),
                    'update_item'       => __('Update Service Category', 'fluxstack'),
                    'add_new_item'      => __('Add New Service Category', 'fluxstack'),
                    'new_item_name'     => __('New Service Category Name', 'fluxstack'),
                    'menu_name'         => __('Service Categories', 'fluxstack'),
                ),
            ),
        );
    }

    protected static function get_custom_columns() {
        return array(
            'thumbnail'    => __('Thumbnail', 'fluxstack'),
            'service_icon' => __('Icon', 'fluxstack'),
        );
    }

    protected static function get_sortable_columns() {
        return array(
            'menu_order' => 'menu_order',
        );
    }

    public static function render_column($column, $post_id) {
        switch ($column) {
            case 'service_icon':
                $icon = get_field('service_icon', $post_id);
                if ($icon) {
                    echo '<span class="dashicons ' . esc_attr($icon) . '"></span>';
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
        if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'service') {
            return;
        }

        if ($query->get('orderby') === 'menu_order') {
            $query->set('orderby', 'menu_order');
            $query->set('order', 'ASC');
        }
    }

    /**
     * Get services
     *
     * @param array $args Query arguments.
     * @return WP_Query
     */
    public static function get_services($args = array()) {
        return self::get_items($args);
    }
}
