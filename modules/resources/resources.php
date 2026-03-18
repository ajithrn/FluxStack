<?php
/**
 * Resources Module
 *
 * Manages the 'resource' custom post type for downloadable
 * documents: agreements, wage sheets, forms, brochures, etc.
 *
 * Taxonomies: Trade, Resource Type, Location/Chapter.
 *
 * @package FluxStack
 */

class FS_Resources extends FS_Base_CPT_Module {
    protected static function get_module_dir() {
        return 'resources';
    }

    protected static function get_acf_group_id() {
        return 'group_resources_meta_fields';
    }

    protected static function get_post_type_config() {
        return array(
            'post_type'     => 'resource',
            'slug'          => 'resource',
            'menu_icon'     => 'dashicons-media-document',
            'menu_position' => 25,
            'supports'      => array('title', 'custom-fields'),
            'labels'        => array(
                'name'               => _x('Resources', 'Post Type General Name', 'fluxstack'),
                'singular_name'      => _x('Resource', 'Post Type Singular Name', 'fluxstack'),
                'menu_name'          => __('Resources', 'fluxstack'),
                'name_admin_bar'     => __('Resource', 'fluxstack'),
                'all_items'          => __('All Resources', 'fluxstack'),
                'add_new_item'       => __('Add New Resource', 'fluxstack'),
                'add_new'            => __('Add New', 'fluxstack'),
                'new_item'           => __('New Resource', 'fluxstack'),
                'edit_item'          => __('Edit Resource', 'fluxstack'),
                'update_item'        => __('Update Resource', 'fluxstack'),
                'view_item'          => __('View Resource', 'fluxstack'),
                'search_items'       => __('Search Resources', 'fluxstack'),
                'not_found'          => __('Not found', 'fluxstack'),
                'not_found_in_trash' => __('Not found in Trash', 'fluxstack'),
            ),
        );
    }

    protected static function get_taxonomy_config() {
        return array(
            // Trade (Plumbers, Sheet Metal, Pipe Fitters, etc.)
            array(
                'taxonomy' => 'resource_trade',
                'slug'     => 'trade',
                'labels'   => array(
                    'name'              => _x('Trades', 'taxonomy general name', 'fluxstack'),
                    'singular_name'     => _x('Trade', 'taxonomy singular name', 'fluxstack'),
                    'search_items'      => __('Search Trades', 'fluxstack'),
                    'all_items'         => __('All Trades', 'fluxstack'),
                    'parent_item'       => __('Parent Trade', 'fluxstack'),
                    'parent_item_colon' => __('Parent Trade:', 'fluxstack'),
                    'edit_item'         => __('Edit Trade', 'fluxstack'),
                    'update_item'       => __('Update Trade', 'fluxstack'),
                    'add_new_item'      => __('Add New Trade', 'fluxstack'),
                    'new_item_name'     => __('New Trade Name', 'fluxstack'),
                    'menu_name'         => __('Trades', 'fluxstack'),
                ),
            ),
            // Resource Type (Agreement, Wage Sheet, Form, Brochure, etc.)
            array(
                'taxonomy' => 'resource_type',
                'slug'     => 'resource-type',
                'labels'   => array(
                    'name'              => _x('Resource Types', 'taxonomy general name', 'fluxstack'),
                    'singular_name'     => _x('Resource Type', 'taxonomy singular name', 'fluxstack'),
                    'search_items'      => __('Search Resource Types', 'fluxstack'),
                    'all_items'         => __('All Resource Types', 'fluxstack'),
                    'parent_item'       => __('Parent Resource Type', 'fluxstack'),
                    'parent_item_colon' => __('Parent Resource Type:', 'fluxstack'),
                    'edit_item'         => __('Edit Resource Type', 'fluxstack'),
                    'update_item'       => __('Update Resource Type', 'fluxstack'),
                    'add_new_item'      => __('Add New Resource Type', 'fluxstack'),
                    'new_item_name'     => __('New Resource Type Name', 'fluxstack'),
                    'menu_name'         => __('Resource Types', 'fluxstack'),
                ),
            ),
            // Location/Chapter (Local 100, Gulf Coast, National, etc.)
            array(
                'taxonomy' => 'resource_location',
                'slug'     => 'resource-location',
                'labels'   => array(
                    'name'              => _x('Locations', 'taxonomy general name', 'fluxstack'),
                    'singular_name'     => _x('Location', 'taxonomy singular name', 'fluxstack'),
                    'search_items'      => __('Search Locations', 'fluxstack'),
                    'all_items'         => __('All Locations', 'fluxstack'),
                    'parent_item'       => __('Parent Location', 'fluxstack'),
                    'parent_item_colon' => __('Parent Location:', 'fluxstack'),
                    'edit_item'         => __('Edit Location', 'fluxstack'),
                    'update_item'       => __('Update Location', 'fluxstack'),
                    'add_new_item'      => __('Add New Location', 'fluxstack'),
                    'new_item_name'     => __('New Location Name', 'fluxstack'),
                    'menu_name'         => __('Locations', 'fluxstack'),
                ),
            ),
        );
    }

    protected static function get_custom_columns() {
        return array(
            'resource_type'   => __('Type', 'fluxstack'),
            'resource_trade'  => __('Trade', 'fluxstack'),
            'access_level'    => __('Access', 'fluxstack'),
        );
    }

    protected static function get_sortable_columns() {
        return array(
            'resource_type' => 'resource_type',
        );
    }

    public static function render_column($column, $post_id) {
        switch ($column) {
            case 'resource_type':
                $terms = get_the_terms($post_id, 'resource_type');
                echo $terms && !is_wp_error($terms)
                    ? esc_html(implode(', ', wp_list_pluck($terms, 'name')))
                    : '—';
                break;

            case 'resource_trade':
                $terms = get_the_terms($post_id, 'resource_trade');
                echo $terms && !is_wp_error($terms)
                    ? esc_html(implode(', ', wp_list_pluck($terms, 'name')))
                    : '—';
                break;

            case 'access_level':
                $level = get_field('resource_access_level', $post_id);
                if ($level === 'members-only') {
                    echo '<span style="color:#d63638;">🔒 Members Only</span>';
                } else {
                    echo '<span style="color:#00a32a;">🌐 Public</span>';
                }
                break;

            default:
                parent::render_column($column, $post_id);
                break;
        }
    }

    /**
     * Get resources with optional filtering
     *
     * @param array $args Query arguments.
     * @return WP_Query
     */
    public static function get_resources($args = array()) {
        return self::get_items($args);
    }

    /**
     * Get resources by trade
     *
     * @param string $trade_slug Trade taxonomy slug.
     * @param int    $count      Number of resources (-1 for all).
     * @return WP_Query
     */
    public static function get_by_trade($trade_slug, $count = -1) {
        return self::get_items(array(
            'posts_per_page' => $count,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'resource_trade',
                    'field'    => 'slug',
                    'terms'    => $trade_slug,
                ),
            ),
        ));
    }

    /**
     * Get resources by type
     *
     * @param string $type_slug Resource type taxonomy slug.
     * @param int    $count     Number of resources (-1 for all).
     * @return WP_Query
     */
    public static function get_by_type($type_slug, $count = -1) {
        return self::get_items(array(
            'posts_per_page' => $count,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'resource_type',
                    'field'    => 'slug',
                    'terms'    => $type_slug,
                ),
            ),
        ));
    }

    /**
     * Get only public resources
     *
     * @param int $count Number of resources (-1 for all).
     * @return WP_Query
     */
    public static function get_public_resources($count = -1) {
        return self::get_items(array(
            'posts_per_page' => $count,
            'meta_query'     => array(
                array(
                    'key'     => 'resource_access_level',
                    'value'   => 'public',
                    'compare' => '=',
                ),
            ),
        ));
    }
}
