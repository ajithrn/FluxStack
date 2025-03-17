<?php
/**
 * Theme Options Module
 *
 * @package FluxStack
 */

class FS_Theme_Options {
    const GENERAL_SETTINGS_GROUP_ID = 'group_fluxstack_general_settings';
    const FOOTER_SETTINGS_GROUP_ID = 'group_fluxstack_footer_settings';
    private static $acf_json_path;

    public static function init() {
        self::$acf_json_path = get_stylesheet_directory() . '/modules/theme-options/acf-json';
        
        add_action('acf/init', array(__CLASS__, 'register_options_pages'));
        add_filter('acf/settings/load_json', array(__CLASS__, 'add_acf_json_load_point'));
        add_action('acf/update_field_group', array(__CLASS__, 'update_field_group'), 1, 1);
    }

    /**
     * Register Theme Options Pages
     */
    public static function register_options_pages() {
        if (!function_exists('acf_add_options_page')) {
            return;
        }

        acf_add_options_page(array(
            'page_title'    => __('Theme Options', 'fluxstack'),
            'menu_title'    => __('Theme Options', 'fluxstack'),
            'menu_slug'     => 'theme-options',
            'capability'    => 'manage_options',
            'redirect'      => false,
            'icon_url'      => 'dashicons-admin-customizer',
            'position'      => 59,
        ));

        acf_add_options_sub_page(array(
            'page_title'    => __('Header Settings', 'fluxstack'),
            'menu_title'    => __('Header', 'fluxstack'),
            'parent_slug'   => 'theme-options',
        ));

        acf_add_options_sub_page(array(
            'page_title'    => __('Footer Settings', 'fluxstack'),
            'menu_title'    => __('Footer', 'fluxstack'),
            'parent_slug'   => 'theme-options',
        ));
    }

    /**
     * Add ACF JSON load point
     *
     * @param array $paths Existing ACF JSON load points
     * @return array Modified array of load points
     */
    public static function add_acf_json_load_point($paths) {
        $paths[] = self::$acf_json_path;
        return $paths;
    }

    /**
     * Handle ACF field group updates
     *
     * @param array $group The field group being updated
     */
    public static function update_field_group($group) {
        if ($group['key'] === self::GENERAL_SETTINGS_GROUP_ID || 
            $group['key'] === self::FOOTER_SETTINGS_GROUP_ID) {
            add_filter('acf/settings/save_json', function() {
                return self::$acf_json_path;
            });
        }
    }

    /**
     * Get a theme option value
     *
     * @param string $option_name Option name
     * @param mixed $default Default value
     * @return mixed Option value
     */
    public static function get_option($option_name, $default = '') {
        if (function_exists('get_field')) {
            return get_field($option_name, 'option') ?? $default;
        }
        return $default;
    }

    /**
     * Get copyright text with year placeholder replaced
     *
     * @return string Formatted copyright text
     */
    public static function get_copyright_text() {
        $copyright = self::get_option('copyright_text', '© {year} Your Company. All rights reserved.');
        return str_replace('{year}', date('Y'), $copyright);
    }

    /**
     * Get payroll button settings
     *
     * @return array|false Button settings or false if not set
     */
    public static function get_payroll_button() {
        return self::get_option('footer_button');
    }
}

// Initialize the theme options module
FS_Theme_Options::init();
