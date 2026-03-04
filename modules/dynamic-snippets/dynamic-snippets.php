<?php
/**
 * Dynamic Snippets
 *
 * Registers Bricks Builder dynamic data snippets.
 * Universal snippets are auto-discovered from the snippets/ directory.
 * Module-specific snippets should be registered by each module's own init().
 *
 * @package FluxStack
 */

class FS_Dynamic_Snippets {
    public static function init() {

        // Register hooks
        add_filter('bricks/dynamic_data/register_snippets', array(__CLASS__, 'register_snippets'));
        add_filter('bricks/dynamic_data/register_categories', array(__CLASS__, 'register_categories'));
    }

    /**
     * Register Dynamic Snippets for Bricks
     *
     * Auto-discovers snippet files from the snippets/ directory.
     * Each file should return an array with: name, label, category, render.
     *
     * @param array $snippets Array of registered snippets.
     * @return array Modified snippets array.
     */
    public static function register_snippets($snippets) {
        $snippets_dir = dirname(__FILE__) . '/snippets';

        if (!is_dir($snippets_dir)) {
            return $snippets;
        }

        $snippet_files = glob($snippets_dir . '/*.php');

        if ($snippet_files) {
            foreach ($snippet_files as $file) {
                $snippet = include $file;

                if (is_array($snippet) && isset($snippet['name']) && isset($snippet['render'])) {
                    $key = 'fluxstack_' . $snippet['name'];
                    $snippets[$key] = array(
                        'name'     => $snippet['name'],
                        'label'    => isset($snippet['label']) ? $snippet['label'] : $snippet['name'],
                        'category' => isset($snippet['category']) ? $snippet['category'] : 'fluxstack',
                        'render'   => $snippet['render'],
                    );
                }
            }
        }

        return $snippets;
    }

    /**
     * Register Dynamic Tags Category
     *
     * @param array $categories Array of registered categories.
     * @return array Modified categories array.
     */
    public static function register_categories($categories) {
        $categories['fluxstack'] = array(
            'label' => __('FluxStack', 'fluxstack'),
            'icon'  => 'fas fa-cube',
        );

        return $categories;
    }
}
