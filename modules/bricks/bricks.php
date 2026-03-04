<?php
/**
 * Bricks Builder Customizations
 *
 * Provides integration with Bricks Builder including custom element
 * registration and utility methods for builder state detection.
 *
 * @package FluxStack
 */

class FS_Bricks {
    public static function init() {
        // Register custom elements when Bricks is ready
        add_action('init', array(__CLASS__, 'register_elements'), 11);
    }

    /**
     * Register custom Bricks Builder elements
     *
     * Elements should be placed in modules/bricks/elements/ directory.
     * Each element should be a PHP file containing a class that extends
     * \Bricks\Element.
     *
     * @see https://academy.bricksbuilder.io/topic/create-your-own-elements/
     */
    public static function register_elements() {
        // Only register if Bricks is active and the Elements class exists
        if (!defined('BRICKS_VERSION') || !class_exists('\Bricks\Elements')) {
            return;
        }

        $elements_dir = get_stylesheet_directory() . '/modules/bricks/elements';

        if (!is_dir($elements_dir)) {
            return;
        }

        // Auto-discover and register elements
        $element_files = glob($elements_dir . '/*.php');

        if ($element_files) {
            foreach ($element_files as $element_file) {
                \Bricks\Elements::register_element($element_file);
            }
        }
    }

    /**
     * Check if current page is using Bricks template
     *
     * @return bool True if using Bricks template.
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
     * Check if Bricks builder is active (editing mode)
     *
     * @return bool True if Bricks builder is active.
     */
    public static function is_builder_active() {
        if (function_exists('bricks_is_builder_main') && function_exists('bricks_is_builder_iframe')) {
            return bricks_is_builder_main() || bricks_is_builder_iframe();
        }
        return false;
    }
}
