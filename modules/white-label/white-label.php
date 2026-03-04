<?php
/**
 * White Label Module
 *
 * Customizes the WordPress admin interface with configurable branding.
 * Settings are stored in wp_options and configurable via FluxStack Settings.
 *
 * @package FluxStack
 */

class FS_White_Label {
    /**
     * Option name for storing white label settings
     */
    const OPTION_NAME = 'fluxstack_white_label_settings';

    /**
     * Default settings (used as fallback and for reset)
     */
    private static $defaults = array(
        'agency_name'    => 'FluxStack',
        'agency_url'     => 'https://fluxstack.dev',
        'platform_name'  => 'FluxStack',
        'footer_text'    => 'Created with %s by <a href="%s" target="_blank">%s</a>',
    );

    public static function init() {
        // Admin footer customization
        add_filter('admin_footer_text', array(__CLASS__, 'update_footer_left'), 9999);
        add_filter('update_footer', array(__CLASS__, 'update_footer_right'), 9999);

        // Remove core block patterns
        add_action('init', array(__CLASS__, 'remove_core_patterns'));

        // Remove WordPress version
        remove_action('wp_head', 'wp_generator');

        // Customize login page
        add_filter('login_headerurl', array(__CLASS__, 'login_header_url'));
        add_filter('login_headertext', array(__CLASS__, 'login_header_text'));

        // Load admin styles
        add_action('admin_enqueue_scripts', array(__CLASS__, 'load_admin_styles'));
        add_action('login_enqueue_scripts', array(__CLASS__, 'load_admin_styles'));
    }

    /**
     * Get all white label settings
     *
     * @return array Settings array with defaults applied.
     */
    public static function get_settings() {
        $settings = get_option(self::OPTION_NAME, array());
        return wp_parse_args($settings, self::$defaults);
    }

    /**
     * Get a single white label setting
     *
     * @param string $key     Setting key.
     * @param mixed  $default Default value (falls back to built-in default).
     * @return mixed Setting value.
     */
    public static function get_setting($key, $default = null) {
        $settings = self::get_settings();

        if (isset($settings[$key])) {
            return $settings[$key];
        }

        return $default !== null ? $default : (isset(self::$defaults[$key]) ? self::$defaults[$key] : '');
    }

    /**
     * Get default settings (for reset functionality)
     *
     * @return array Default settings.
     */
    public static function get_defaults() {
        return self::$defaults;
    }

    /**
     * Load admin styles
     */
    public static function load_admin_styles() {
        $css_path = get_stylesheet_directory_uri() . '/modules/white-label/assets/css/admin.min.css';
        $version = wp_get_theme()->get('Version');

        wp_enqueue_style('fluxstack-admin', $css_path, array(), $version);
    }

    /**
     * Update admin footer left text
     */
    public static function update_footer_left() {
        $settings = self::get_settings();

        return sprintf(
            $settings['footer_text'],
            esc_html($settings['platform_name']),
            esc_url($settings['agency_url']),
            esc_html($settings['agency_name'])
        );
    }

    /**
     * Update admin footer right text
     */
    public static function update_footer_right() {
        return date("F j, Y, g:i a");
    }

    /**
     * Remove core block patterns
     */
    public static function remove_core_patterns() {
        remove_theme_support('core-block-patterns');
    }

    /**
     * Customize login header URL
     */
    public static function login_header_url() {
        return self::get_setting('agency_url');
    }

    /**
     * Customize login header text
     */
    public static function login_header_text() {
        return self::get_setting('agency_name');
    }
}
