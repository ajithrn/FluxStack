<?php
/**
 * Member Directory Module
 *
 * Manages the 'member' custom post type for the Member Directory.
 *
 * @package FluxStack
 */

class FS_Member_Directory extends FS_Base_CPT_Module {
    protected static function get_module_dir() {
        return 'member-directory';
    }

    protected static function get_acf_group_id() {
        return 'group_member_directory_meta_fields';
    }

    protected static function get_post_type_config() {
        return array(
            'post_type'     => 'member',
            'slug'          => 'member',
            'menu_icon'     => 'dashicons-groups',
            'menu_position' => 26,
            'supports'      => array('title', 'thumbnail'),
            'labels'        => array(
                'name'               => _x('Members', 'Post Type General Name', 'fluxstack'),
                'singular_name'      => _x('Member', 'Post Type Singular Name', 'fluxstack'),
                'menu_name'          => __('Member Directory', 'fluxstack'),
                'name_admin_bar'     => __('Member', 'fluxstack'),
                'all_items'          => __('All Members', 'fluxstack'),
                'add_new_item'       => __('Add New Member', 'fluxstack'),
                'add_new'            => __('Add New', 'fluxstack'),
                'new_item'           => __('New Member', 'fluxstack'),
                'edit_item'          => __('Edit Member', 'fluxstack'),
                'update_item'        => __('Update Member', 'fluxstack'),
                'view_item'          => __('View Member', 'fluxstack'),
                'search_items'       => __('Search Members', 'fluxstack'),
                'not_found'          => __('Not found', 'fluxstack'),
                'not_found_in_trash' => __('Not found in Trash', 'fluxstack'),
            ),
        );
    }

    protected static function get_taxonomy_config() {
        return array(
            // Member Type (Mechanical Contractor, Associate Member, etc.)
            array(
                'taxonomy' => 'member_type',
                'slug'     => 'member-type',
                'labels'   => array(
                    'name'              => _x('Member Types', 'taxonomy general name', 'fluxstack'),
                    'singular_name'     => _x('Member Type', 'taxonomy singular name', 'fluxstack'),
                    'search_items'      => __('Search Member Types', 'fluxstack'),
                    'all_items'         => __('All Member Types', 'fluxstack'),
                    'parent_item'       => __('Parent Member Type', 'fluxstack'),
                    'parent_item_colon' => __('Parent Member Type:', 'fluxstack'),
                    'edit_item'         => __('Edit Member Type', 'fluxstack'),
                    'update_item'       => __('Update Member Type', 'fluxstack'),
                    'add_new_item'      => __('Add New Member Type', 'fluxstack'),
                    'new_item_name'     => __('New Member Type Name', 'fluxstack'),
                    'menu_name'         => __('Member Types', 'fluxstack'),
                ),
            ),
        );
    }

    protected static function get_custom_columns() {
        return array(
            'member_type' => __('Type', 'fluxstack'),
            'phone'       => __('Phone', 'fluxstack'),
            'website'     => __('Website', 'fluxstack'),
        );
    }

    protected static function get_sortable_columns() {
        return array(
            'member_type' => 'member_type',
        );
    }

    public static function render_column($column, $post_id) {
        switch ($column) {
            case 'member_type':
                $terms = get_the_terms($post_id, 'member_type');
                echo $terms && !is_wp_error($terms)
                    ? esc_html(implode(', ', wp_list_pluck($terms, 'name')))
                    : '—';
                break;

            case 'phone':
                $phone = get_field('member_phone', $post_id);
                echo $phone ? esc_html($phone) : '—';
                break;

            case 'website':
                $website = get_field('member_website', $post_id);
                if ($website) {
                    echo '<a href="' . esc_url($website) . '" target="_blank">' . esc_html($website) . '</a>';
                } else {
                    echo '—';
                }
                break;

            default:
                parent::render_column($column, $post_id);
                break;
        }
    }

    /**
     * Get members with optional filtering
     *
     * @param array $args Query arguments.
     * @return WP_Query
     */
    public static function get_members($args = array()) {
        return self::get_items($args);
    }

    /**
     * Get members by type
     *
     * @param string $type_slug Member type taxonomy slug.
     * @param int    $count     Number of members (-1 for all).
     * @return WP_Query
     */
    public static function get_by_type($type_slug, $count = -1) {
        return self::get_items(array(
            'posts_per_page' => $count,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'member_type',
                    'field'    => 'slug',
                    'terms'    => $type_slug,
                ),
            ),
        ));
    }
}
