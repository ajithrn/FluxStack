<?php
/**
 * Theme Options Module
 *
 * Manages ACF options pages for universal site settings:
 * branding, contact info, social media, footer.
 *
 * Site-specific options are added via ACF JSON in acf-json/custom/.
 * SEO functionality is handled by the separate SEO module.
 *
 * @package FluxStack
 */

// Load includes
require_once dirname(__FILE__) . '/includes/acf-config.php';
require_once dirname(__FILE__) . '/includes/github-updater.php';

class FS_Theme_Options {
    const GENERAL_SETTINGS_GROUP_ID = 'group_fluxstack_general_settings';
    const FOOTER_SETTINGS_GROUP_ID = 'group_fluxstack_footer_settings';

    private static $acf_json_path;
    private static $acf_custom_path;

    /**
     * Sub-pages conditionally registered when matching ACF JSON exists in custom/.
     */
    private static $conditional_sub_pages = array(
        'acf-options-header' => array(
            'page_title' => 'Header Settings',
            'menu_title' => 'Header',
        ),
        'acf-options-home-page' => array(
            'page_title' => 'Home Page Settings',
            'menu_title' => 'Home Page',
        ),
    );

    /**
     * Initialize the module
     */
    public static function init() {
        self::$acf_json_path = get_stylesheet_directory() . '/modules/theme-options/acf-json';
        self::$acf_custom_path = self::$acf_json_path . '/custom';

        // Initialize ACF config (load/save points)
        FS_Theme_Options_ACF::init(
            self::$acf_json_path,
            self::$acf_custom_path,
            array(self::GENERAL_SETTINGS_GROUP_ID, self::FOOTER_SETTINGS_GROUP_ID)
        );

        // Register options pages
        add_action('acf/init', array(__CLASS__, 'register_options_pages'));

        // Admin UI
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_styles'));
        add_filter('admin_body_class', array(__CLASS__, 'add_admin_body_class'));

        // GitHub auto-updater (opt-in via constant)
        if (defined('FLUXSTACK_GITHUB_REPO') && FLUXSTACK_GITHUB_REPO) {
            new FS_GitHub_Updater(FLUXSTACK_GITHUB_REPO);
        }
    }

    // =========================================================================
    // Admin UI
    // =========================================================================

    /**
     * Check if current admin page is a FluxStack options page
     */
    private static function is_options_page() {
        if (!is_admin()) {
            return false;
        }
        $screen = get_current_screen();
        if (!$screen) {
            return false;
        }
        return (strpos($screen->id, 'theme-options') !== false || strpos($screen->id, 'acf-options') !== false);
    }

    /**
     * Add custom body class for CSS scoping
     */
    public static function add_admin_body_class($classes) {
        if (self::is_options_page()) {
            $classes .= ' fluxstack-options-page';
        }
        return $classes;
    }

    /**
     * Enqueue admin styles on options pages
     */
    public static function enqueue_admin_styles() {
        if (!self::is_options_page()) {
            return;
        }

        wp_enqueue_style(
            'fluxstack-theme-options-admin',
            get_stylesheet_directory_uri() . '/modules/theme-options/assets/css/admin.css',
            array(),
            wp_get_theme()->get('Version')
        );
    }

    // =========================================================================
    // Options Page Registration
    // =========================================================================

    /**
     * Register ACF options pages
     */
    public static function register_options_pages() {
        if (!function_exists('acf_add_options_page')) {
            return;
        }

        // Main options page (always registered)
        acf_add_options_page(array(
            'page_title'    => __('Theme Options', 'fluxstack'),
            'menu_title'    => __('Theme Options', 'fluxstack'),
            'menu_slug'     => 'theme-options',
            'capability'    => 'manage_options',
            'redirect'      => false,
            'icon_url'      => 'dashicons-admin-customizer',
            'position'      => 59,
        ));

        // Footer sub-page (always registered — universal)
        acf_add_options_sub_page(array(
            'page_title'    => __('Footer Settings', 'fluxstack'),
            'menu_title'    => __('Footer', 'fluxstack'),
            'parent_slug'   => 'theme-options',
        ));

        // Conditionally register sub-pages based on custom ACF JSON
        $targeted = FS_Theme_Options_ACF::get_targeted_pages();
        foreach (self::$conditional_sub_pages as $slug => $config) {
            if (isset($targeted[$slug])) {
                acf_add_options_sub_page(array(
                    'page_title'    => __($config['page_title'], 'fluxstack'),
                    'menu_title'    => __($config['menu_title'], 'fluxstack'),
                    'parent_slug'   => 'theme-options',
                ));
            }
        }
    }

    /**
     * Get conditional sub-pages config (used by ACF config)
     *
     * @return array
     */
    public static function get_conditional_sub_pages() {
        return self::$conditional_sub_pages;
    }

    // =========================================================================
    // Public API — Getters
    // =========================================================================

    /**
     * Get a theme option value
     *
     * @param string $option_name Option name.
     * @param mixed  $default     Default value.
     * @return mixed
     */
    public static function get_option($option_name, $default = '') {
        if (function_exists('get_field')) {
            return get_field($option_name, 'option') ?? $default;
        }
        return $default;
    }

    /**
     * Get contact info as an array
     *
     * @return array Contact info with keys: email, phone, address.
     */
    public static function get_contact_info() {
        return array(
            'email'   => self::get_option('contact_email'),
            'phone'   => self::get_option('contact_phone'),
            'address' => self::get_option('contact_address'),
        );
    }

    /**
     * Get copyright text with placeholders replaced
     *
     * @return string Formatted copyright text.
     */
    public static function get_copyright_text() {
        $text = self::get_option('copyright_text', '© {year} Your Company. All rights reserved.');
        $text = str_replace(array('{year}', '[year]'), date('Y'), $text);
        $text = str_replace(array('{site_name}', '[site_name]'), get_bloginfo('name'), $text);
        return $text;
    }

    /**
     * Get footer button settings
     *
     * @return array|false Button settings or false if not set.
     */
    public static function get_footer_button() {
        return self::get_option('footer_button');
    }
}
