<?php
/**
 * Theme Options Module
 *
 * Manages ACF options pages and outputs SEO meta tags.
 * Provides the universal settings every site needs (branding,
 * contact, social, footer, SEO). Site-specific options are
 * added via ACF JSON files in acf-json/custom/.
 *
 * @package FluxStack
 */

class FS_Theme_Options {
    const GENERAL_SETTINGS_GROUP_ID = 'group_fluxstack_general_settings';
    const FOOTER_SETTINGS_GROUP_ID = 'group_fluxstack_footer_settings';
    private static $acf_json_path;
    private static $acf_custom_path;

    /**
     * Sub-pages that are conditionally registered when
     * a matching ACF field group exists in acf-json/custom/.
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

    public static function init() {
        self::$acf_json_path = get_stylesheet_directory() . '/modules/theme-options/acf-json';
        self::$acf_custom_path = self::$acf_json_path . '/custom';

        add_action('acf/init', array(__CLASS__, 'register_options_pages'));
        add_filter('acf/settings/load_json', array(__CLASS__, 'add_acf_json_load_points'));
        add_action('acf/update_field_group', array(__CLASS__, 'update_field_group'), 1, 1);

        // Admin styles for options pages
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_styles'));

        // Add custom body class for CSS scoping
        add_filter('admin_body_class', array(__CLASS__, 'add_admin_body_class'));

        // SEO meta output
        add_action('wp_head', array(__CLASS__, 'output_seo_meta'), 1);
    }

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
        // Match toplevel_page_theme-options and theme-options_page_acf-options-*
        return (strpos($screen->id, 'theme-options') !== false || strpos($screen->id, 'acf-options') !== false);
    }

    /**
     * Add custom body class on ACF options pages
     */
    public static function add_admin_body_class($classes) {
        if (self::is_options_page()) {
            $classes .= ' fluxstack-options-page';
        }
        return $classes;
    }

    /**
     * Enqueue admin styles on ACF options pages
     */
    public static function enqueue_admin_styles() {
        if (!self::is_options_page()) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        wp_enqueue_style(
            'fluxstack-theme-options-admin',
            get_stylesheet_directory_uri() . '/modules/theme-options/assets/css/admin.css',
            array(),
            $version
        );
    }

    /**
     * Register Theme Options Pages
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

        // Conditionally register sub-pages based on custom ACF JSON files
        self::register_conditional_sub_pages();
    }

    /**
     * Register sub-pages only when matching ACF field groups exist
     * in the acf-json/custom/ directory.
     */
    private static function register_conditional_sub_pages() {
        if (!is_dir(self::$acf_custom_path)) {
            return;
        }

        $json_files = glob(self::$acf_custom_path . '/*.json');
        if (empty($json_files)) {
            return;
        }

        // Scan custom JSON files for options_page location rules
        $targeted_pages = array();
        foreach ($json_files as $file) {
            $content = file_get_contents($file);
            $group = json_decode($content, true);

            if (!empty($group['location'])) {
                foreach ($group['location'] as $rule_group) {
                    foreach ($rule_group as $rule) {
                        if (isset($rule['param']) && $rule['param'] === 'options_page' && isset($rule['value'])) {
                            $targeted_pages[$rule['value']] = true;
                        }
                    }
                }
            }
        }

        // Register sub-pages that have matching field groups
        foreach (self::$conditional_sub_pages as $slug => $config) {
            if (isset($targeted_pages[$slug])) {
                acf_add_options_sub_page(array(
                    'page_title'    => __($config['page_title'], 'fluxstack'),
                    'menu_title'    => __($config['menu_title'], 'fluxstack'),
                    'parent_slug'   => 'theme-options',
                ));
            }
        }
    }

    /**
     * Add ACF JSON load points (base + custom)
     *
     * @param array $paths Existing ACF JSON load points.
     * @return array Modified array of load points.
     */
    public static function add_acf_json_load_points($paths) {
        $paths[] = self::$acf_json_path;

        // Also load from custom/ directory for per-client fields
        if (is_dir(self::$acf_custom_path)) {
            $paths[] = self::$acf_custom_path;
        }

        return $paths;
    }

    /**
     * Handle ACF field group updates — save to correct directory
     *
     * @param array $group The field group being updated.
     */
    public static function update_field_group($group) {
        // Base theme field groups save to acf-json/
        $base_groups = array(
            self::GENERAL_SETTINGS_GROUP_ID,
            self::FOOTER_SETTINGS_GROUP_ID,
        );

        if (in_array($group['key'], $base_groups)) {
            add_filter('acf/settings/save_json', function() {
                return self::$acf_json_path;
            });
            return;
        }

        // Check if this group targets a conditional options page — save to custom/
        if (!empty($group['location'])) {
            foreach ($group['location'] as $rule_group) {
                foreach ($rule_group as $rule) {
                    if (isset($rule['param']) && $rule['param'] === 'options_page') {
                        $slug = $rule['value'];
                        if (isset(self::$conditional_sub_pages[$slug])) {
                            // Ensure custom directory exists
                            if (!is_dir(self::$acf_custom_path)) {
                                wp_mkdir_p(self::$acf_custom_path);
                            }
                            add_filter('acf/settings/save_json', function() {
                                return self::$acf_custom_path;
                            });
                            return;
                        }
                    }
                }
            }
        }
    }

    /**
     * Get a theme option value
     *
     * @param string $option_name Option name.
     * @param mixed  $default     Default value.
     * @return mixed Option value.
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
     * Get copyright text with year placeholder replaced
     *
     * @return string Formatted copyright text.
     */
    public static function get_copyright_text() {
        $copyright = self::get_option('copyright_text', '© {year} Your Company. All rights reserved.');
        return str_replace('{year}', date('Y'), $copyright);
    }

    /**
     * Get footer button settings
     *
     * @return array|false Button settings or false if not set.
     */
    public static function get_footer_button() {
        return self::get_option('footer_button');
    }

    /**
     * Output SEO meta tags in wp_head
     */
    public static function output_seo_meta() {
        // Default meta description
        $meta_description = self::get_option('seo_meta_description');
        if ($meta_description && !is_singular()) {
            echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
        }

        // OG image fallback
        $og_image = self::get_option('seo_og_image');
        if ($og_image && is_array($og_image) && !empty($og_image['url'])) {
            // Only output if current page doesn't have its own featured image
            if (!is_singular() || !has_post_thumbnail()) {
                echo '<meta property="og:image" content="' . esc_url($og_image['url']) . '">' . "\n";
                if (!empty($og_image['width'])) {
                    echo '<meta property="og:image:width" content="' . esc_attr($og_image['width']) . '">' . "\n";
                }
                if (!empty($og_image['height'])) {
                    echo '<meta property="og:image:height" content="' . esc_attr($og_image['height']) . '">' . "\n";
                }
            }
        }

        // Google Site Verification
        $verification = self::get_option('seo_google_verification');
        if ($verification) {
            echo '<meta name="google-site-verification" content="' . esc_attr($verification) . '">' . "\n";
        }

        // Google Analytics / GTM
        $analytics_id = self::get_option('seo_analytics_id');
        if ($analytics_id) {
            $analytics_id = trim($analytics_id);
            if (strpos($analytics_id, 'GTM-') === 0) {
                // Google Tag Manager
                echo "<!-- Google Tag Manager -->\n";
                echo "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':\n";
                echo "new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],\n";
                echo "j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=\n";
                echo "'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);\n";
                echo "})(window,document,'script','dataLayer','" . esc_js($analytics_id) . "');</script>\n";
                echo "<!-- End Google Tag Manager -->\n";
            } elseif (strpos($analytics_id, 'G-') === 0 || strpos($analytics_id, 'UA-') === 0) {
                // Google Analytics 4 or Universal Analytics
                echo "<!-- Google Analytics -->\n";
                echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . esc_attr($analytics_id) . '"></script>' . "\n";
                echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}\n";
                echo "gtag('js',new Date());gtag('config','" . esc_js($analytics_id) . "');</script>\n";
                echo "<!-- End Google Analytics -->\n";
            }
        }

        // Additional head scripts
        $head_scripts = self::get_option('seo_head_scripts');
        if ($head_scripts) {
            echo "\n" . $head_scripts . "\n";
        }
    }
}
