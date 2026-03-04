<?php
/**
 * SEO Module
 *
 * Provides basic sitewide SEO functionality:
 * - Default meta description (non-singular pages)
 * - OG image fallback
 * - Google Site Verification
 * - Google Analytics / GTM script injection
 * - Additional head scripts
 *
 * Settings are managed via ACF fields in the Theme Options → SEO tab.
 * This module can be toggled on/off via FluxStack Settings.
 *
 * @package FluxStack
 */

class FS_SEO {
    /**
     * Initialize the SEO module
     */
    public static function init() {
        add_action('wp_head', array(__CLASS__, 'output_meta'), 1);
    }

    /**
     * Get an SEO option value
     *
     * @param string $key     Option key (without seo_ prefix).
     * @param mixed  $default Default value.
     * @return mixed
     */
    private static function get_option($key, $default = '') {
        if (class_exists('FS_Theme_Options')) {
            return FS_Theme_Options::get_option('seo_' . $key, $default);
        }
        return $default;
    }

    /**
     * Output all SEO meta tags in wp_head
     */
    public static function output_meta() {
        self::output_meta_description();
        self::output_og_image();
        self::output_google_verification();
        self::output_analytics();
        self::output_head_scripts();
    }

    /**
     * Output default meta description for non-singular pages
     */
    private static function output_meta_description() {
        $description = self::get_option('meta_description');
        if ($description && !is_singular()) {
            echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
        }
    }

    /**
     * Output OG image fallback when page has no featured image
     */
    private static function output_og_image() {
        $og_image = self::get_option('og_image');
        if (!$og_image || !is_array($og_image) || empty($og_image['url'])) {
            return;
        }

        // Only output if current page doesn't have its own featured image
        if (is_singular() && has_post_thumbnail()) {
            return;
        }

        echo '<meta property="og:image" content="' . esc_url($og_image['url']) . '">' . "\n";
        if (!empty($og_image['width'])) {
            echo '<meta property="og:image:width" content="' . esc_attr($og_image['width']) . '">' . "\n";
        }
        if (!empty($og_image['height'])) {
            echo '<meta property="og:image:height" content="' . esc_attr($og_image['height']) . '">' . "\n";
        }
    }

    /**
     * Output Google Site Verification meta tag
     */
    private static function output_google_verification() {
        $code = self::get_option('google_verification');
        if ($code) {
            echo '<meta name="google-site-verification" content="' . esc_attr($code) . '">' . "\n";
        }
    }

    /**
     * Output Google Analytics or GTM script
     * Auto-detects G-, UA-, or GTM- prefix.
     */
    private static function output_analytics() {
        $id = self::get_option('analytics_id');
        if (!$id) {
            return;
        }

        $id = trim($id);

        if (strpos($id, 'GTM-') === 0) {
            self::output_gtm($id);
        } elseif (strpos($id, 'G-') === 0 || strpos($id, 'UA-') === 0) {
            self::output_gtag($id);
        }
    }

    /**
     * Output Google Tag Manager script
     */
    private static function output_gtm($id) {
        echo "<!-- Google Tag Manager -->\n";
        echo "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':\n";
        echo "new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],\n";
        echo "j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=\n";
        echo "'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);\n";
        echo "})(window,document,'script','dataLayer','" . esc_js($id) . "');</script>\n";
        echo "<!-- End Google Tag Manager -->\n";
    }

    /**
     * Output Google Analytics (GA4/UA) script
     */
    private static function output_gtag($id) {
        echo "<!-- Google Analytics -->\n";
        echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . esc_attr($id) . '"></script>' . "\n";
        echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}\n";
        echo "gtag('js',new Date());gtag('config','" . esc_js($id) . "');</script>\n";
        echo "<!-- End Google Analytics -->\n";
    }

    /**
     * Output additional head scripts (admin-entered raw HTML)
     */
    private static function output_head_scripts() {
        $scripts = self::get_option('head_scripts');
        if ($scripts) {
            echo "\n" . $scripts . "\n";
        }
    }
}
