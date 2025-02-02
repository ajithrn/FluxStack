<?php
/**
 * White Label Module
 *
 * @package FluxStack
 */

class FS_White_Label {
    /**
     * Agency/Platform Configuration
     * Edit these constants to customize white labeling
     */
    const AGENCY_NAME = 'FluxStack';
    const AGENCY_URL = 'https://fluxstack.dev';
    const PLATFORM_NAME = 'FluxStack';
    const FOOTER_TEXT = 'Created with %s by <a href="%s" target="_blank">%s</a>';

    /**
     * Admin Color Scheme
     * Edit these constants to customize admin interface colors
     */
    const PRIMARY_COLOR = '#0073aa';
    const SECONDARY_COLOR = '#00a0d2';
    const DARK_COLOR = '#1e1e1e';
    const LIGHT_COLOR = '#f8f9fa';

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
        return sprintf(
            self::FOOTER_TEXT,
            esc_html(self::PLATFORM_NAME),
            esc_url(self::AGENCY_URL),
            esc_html(self::AGENCY_NAME)
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
        return self::AGENCY_URL;
    }

    /**
     * Customize login header text
     */
    public static function login_header_text() {
        return self::AGENCY_NAME;
    }
}

// Initialize the white label module
FS_White_Label::init();
