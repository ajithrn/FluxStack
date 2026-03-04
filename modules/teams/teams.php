<?php
/**
 * Teams Module
 *
 * Manages the 'team' custom post type for team members.
 *
 * @package FluxStack
 */

class FS_Teams extends FS_Base_CPT_Module {
    protected static function get_module_dir() {
        return 'teams';
    }

    protected static function get_acf_group_id() {
        return 'group_teams_meta_fields';
    }

    protected static function get_post_type_config() {
        return array(
            'post_type'     => 'team',
            'slug'          => 'team',
            'menu_icon'     => 'dashicons-groups',
            'menu_position' => 20,
            'supports'      => array('title', 'thumbnail', 'custom-fields'),
            'labels'        => array(
                'name'               => _x('Team Members', 'Post Type General Name', 'fluxstack'),
                'singular_name'      => _x('Team Member', 'Post Type Singular Name', 'fluxstack'),
                'menu_name'          => __('Teams', 'fluxstack'),
                'name_admin_bar'     => __('Team Member', 'fluxstack'),
                'all_items'          => __('All Team Members', 'fluxstack'),
                'add_new_item'       => __('Add New Team Member', 'fluxstack'),
                'add_new'            => __('Add New', 'fluxstack'),
                'new_item'           => __('New Team Member', 'fluxstack'),
                'edit_item'          => __('Edit Team Member', 'fluxstack'),
                'update_item'        => __('Update Team Member', 'fluxstack'),
                'view_item'          => __('View Team Member', 'fluxstack'),
                'search_items'       => __('Search Team Member', 'fluxstack'),
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
                'taxonomy' => 'team_category',
                'slug'     => 'team-category',
                'labels'   => array(
                    'name'              => _x('Team Categories', 'taxonomy general name', 'fluxstack'),
                    'singular_name'     => _x('Team Category', 'taxonomy singular name', 'fluxstack'),
                    'search_items'      => __('Search Team Categories', 'fluxstack'),
                    'all_items'         => __('All Team Categories', 'fluxstack'),
                    'parent_item'       => __('Parent Team Category', 'fluxstack'),
                    'parent_item_colon' => __('Parent Team Category:', 'fluxstack'),
                    'edit_item'         => __('Edit Team Category', 'fluxstack'),
                    'update_item'       => __('Update Team Category', 'fluxstack'),
                    'add_new_item'      => __('Add New Team Category', 'fluxstack'),
                    'new_item_name'     => __('New Team Category Name', 'fluxstack'),
                    'menu_name'         => __('Team Categories', 'fluxstack'),
                ),
            ),
        );
    }

    protected static function get_custom_columns() {
        return array(
            'position'  => __('Position', 'fluxstack'),
            'email'     => __('Email', 'fluxstack'),
            'phone'     => __('Phone', 'fluxstack'),
            'thumbnail' => __('Profile Image', 'fluxstack'),
        );
    }

    protected static function get_sortable_columns() {
        return array(
            'position' => 'position',
        );
    }

    protected static function register_hooks() {
        // Disable default editor and enable seamless mode
        add_action('init', array(__CLASS__, 'disable_default_editor'));
    }

    public static function disable_default_editor() {
        add_action('init', function() {
            remove_post_type_support('team', 'editor');
        }, 99);

        add_filter('acf/settings/seamless', function() {
            return true;
        });
    }

    public static function render_column($column, $post_id) {
        switch ($column) {
            case 'position':
                $position = get_field('team_position', $post_id);
                echo $position ? esc_html($position) : '—';
                break;
            case 'email':
                $email = get_field('team_email', $post_id);
                if ($email) {
                    echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
                } else {
                    echo '—';
                }
                break;
            case 'phone':
                $phone = get_field('team_phone', $post_id);
                echo $phone ? esc_html($phone) : '—';
                break;
            default:
                parent::render_column($column, $post_id);
                break;
        }
    }

    public static function handle_column_sorting($query) {
        if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'team') {
            return;
        }

        if ($query->get('orderby') === 'position') {
            $query->set('meta_key', 'team_position');
            $query->set('orderby', 'meta_value');
        }
    }

    /**
     * Get team members
     *
     * @param array $args Query arguments.
     * @return WP_Query
     */
    public static function get_team_members($args = array()) {
        return self::get_items($args);
    }
}
