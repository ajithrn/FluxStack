<?php
/**
 * Teams Module
 *
 * @package FluxStack
 */

class FS_Teams {
    const ACF_GROUP_ID = 'group_teams_meta_fields';
    private static $acf_json_path;

    public static function init() {
        self::$acf_json_path = get_stylesheet_directory() . '/modules/teams/acf-json';
        
        add_action('init', array(__CLASS__, 'register_post_type'), 0);
        add_action('init', array(__CLASS__, 'register_taxonomy'), 0);
        add_filter('acf/settings/load_json', array(__CLASS__, 'add_acf_json_load_point'));
        add_action('acf/update_field_group', array(__CLASS__, 'update_field_group'), 1, 1);
        
        // Add admin columns
        add_filter('manage_team_posts_columns', array(__CLASS__, 'set_custom_columns'));
        add_action('manage_team_posts_custom_column', array(__CLASS__, 'custom_column_content'), 10, 2);
        add_filter('manage_edit-team_sortable_columns', array(__CLASS__, 'set_sortable_columns'));
        
        // Disable default editor and enable seamless mode
        add_action('init', array(__CLASS__, 'disable_default_editor'));
    }

    /**
     * Disable default editor and enable seamless mode
     */
    public static function disable_default_editor() {
        // Remove editor support from the team post type
        add_action('init', function() {
            remove_post_type_support('team', 'editor');
        }, 99);
        
        // Enable seamless mode for ACF
        add_filter('acf/settings/seamless', function() {
            return true;
        });
    }

    public static function set_custom_columns($columns) {
        $new_columns = array();
        foreach($columns as $key => $value) {
            if($key === 'title') {
                $new_columns[$key] = $value;
                $new_columns['position'] = __('Position', 'fluxstack');
                $new_columns['email'] = __('Email', 'fluxstack');
            } else if($key === 'date') {
                $new_columns['phone'] = __('Phone', 'fluxstack');
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
            case 'position':
                $position = get_field('team_position', $post_id);
                if($position) {
                    echo esc_html($position);
                }
                break;
            case 'email':
                $email = get_field('team_email', $post_id);
                if($email) {
                    echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
                }
                break;
            case 'phone':
                $phone = get_field('team_phone', $post_id);
                if($phone) {
                    echo esc_html($phone);
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
        $columns['position'] = 'position';
        return $columns;
    }

    public static function register_post_type() {
        $labels = array(
            'name'                => _x('Team Members', 'Post Type General Name', 'fluxstack'),
            'singular_name'       => _x('Team Member', 'Post Type Singular Name', 'fluxstack'),
            'menu_name'           => __('Teams', 'fluxstack'),
            'name_admin_bar'      => __('Team Member', 'fluxstack'),
            'parent_item_colon'   => __('Parent Team Member:', 'fluxstack'),
            'all_items'           => __('All Team Members', 'fluxstack'),
            'add_new_item'        => __('Add New Team Member', 'fluxstack'),
            'add_new'             => __('Add New', 'fluxstack'),
            'new_item'            => __('New Team Member', 'fluxstack'),
            'edit_item'           => __('Edit Team Member', 'fluxstack'),
            'update_item'         => __('Update Team Member', 'fluxstack'),
            'view_item'           => __('View Team Member', 'fluxstack'),
            'search_items'        => __('Search Team Member', 'fluxstack'),
            'not_found'           => __('Not found', 'fluxstack'),
            'not_found_in_trash'  => __('Not found in Trash', 'fluxstack'),
        );
        $args = array(
            'label'               => __('team', 'fluxstack'),
            'description'         => __('Team Members', 'fluxstack'),
            'labels'              => $labels,
            'supports'            => array('title', 'thumbnail', 'custom-fields'),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 20,
            'menu_icon'           => 'dashicons-groups',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
            'show_in_rest'        => false,
            'rewrite'            => array('slug' => 'team')
        );
        register_post_type('team', $args);
    }

    public static function register_taxonomy() {
        $labels = array(
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
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'          => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_rest'      => false,
            'rewrite'           => array('slug' => 'team-category'),
        );

        register_taxonomy('team_category', array('team'), $args);
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
}

// Initialize the teams module
FS_Teams::init();
