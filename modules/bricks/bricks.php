<?php
/**
 * Bricks Builder Customizations
 *
 * @package FluxStack
 */

class FS_Bricks {
    public static function init() {
        add_filter('bricks/elements', array(__CLASS__, 'register_elements'));
        add_filter('bricks/templates', array(__CLASS__, 'register_templates'));
        add_filter('bricks/settings', array(__CLASS__, 'register_settings'));
    }

    /**
     * Add custom Bricks Builder elements
     *
     * @param array $elements Array of registered elements.
     * @return array Modified elements array.
     */
    public static function register_elements($elements) {
        // Add custom elements here
        return $elements;
    }

    /**
     * Add custom Bricks Builder templates
     *
     * @param array $templates Array of registered templates.
     * @return array Modified templates array.
     */
    public static function register_templates($templates) {
        // Add custom templates here
        return $templates;
    }

    /**
     * Add custom Bricks Builder settings
     *
     * @param array $settings Array of settings.
     * @return array Modified settings array.
     */
    public static function register_settings($settings) {
        // Add custom settings here
        return $settings;
    }

    /**
     * Check if current page is using Bricks template
     *
     * @return boolean True if using Bricks template.
     */
    public static function is_bricks_template() {
        if (function_exists('bricks_is_builder_main')) {
            return bricks_is_builder_main();
        }
        return false;
    }

    /**
     * Get Bricks template ID for current page
     *
     * @param int|null $post_id Optional post ID.
     * @return int|false Template ID or false if not found.
     */
    public static function get_template_id($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        if (function_exists('bricks_get_template_id')) {
            return bricks_get_template_id($post_id);
        }

        return false;
    }

    /**
     * Check if Bricks builder is active
     *
     * @return boolean True if Bricks builder is active.
     */
    public static function is_builder_active() {
        if (function_exists('bricks_is_builder_main') && function_exists('bricks_is_builder_iframe')) {
            return bricks_is_builder_main() || bricks_is_builder_iframe();
        }
        return false;
    }
}

// Initialize the Bricks module
FS_Bricks::init();
