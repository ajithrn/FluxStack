<?php
/**
 * ACF Configuration
 *
 * Handles ACF JSON load/save points for theme options.
 * Loaded by FS_Theme_Options — not standalone.
 *
 * @package FluxStack
 */

class FS_Theme_Options_ACF {
    private static $json_path;
    private static $custom_path;
    private static $base_group_ids = array();

    /**
     * Initialize ACF config
     *
     * @param string $json_path   Base ACF JSON directory.
     * @param string $custom_path Custom ACF JSON directory.
     * @param array  $base_ids    Base field group IDs.
     */
    public static function init($json_path, $custom_path, $base_ids = array()) {
        self::$json_path = $json_path;
        self::$custom_path = $custom_path;
        self::$base_group_ids = $base_ids;

        add_filter('acf/settings/load_json', array(__CLASS__, 'add_load_points'));
        add_action('acf/update_field_group', array(__CLASS__, 'route_save_path'), 1, 1);
    }

    /**
     * Add ACF JSON load points (base + custom)
     *
     * @param array $paths Existing load points.
     * @return array Modified load points.
     */
    public static function add_load_points($paths) {
        $paths[] = self::$json_path;

        if (is_dir(self::$custom_path)) {
            $paths[] = self::$custom_path;
        }

        return $paths;
    }

    /**
     * Route field group saves to the correct directory
     *
     * @param array $group The field group being updated.
     */
    public static function route_save_path($group) {
        // Base theme field groups → acf-json/
        if (in_array($group['key'], self::$base_group_ids)) {
            add_filter('acf/settings/save_json', function() {
                return self::$json_path;
            });
            return;
        }

        // Groups targeting conditional options pages → acf-json/custom/
        if (!empty($group['location'])) {
            foreach ($group['location'] as $rule_group) {
                foreach ($rule_group as $rule) {
                    if (isset($rule['param']) && $rule['param'] === 'options_page') {
                        $slug = $rule['value'];
                        $conditional = FS_Theme_Options::get_conditional_sub_pages();
                        if (isset($conditional[$slug])) {
                            if (!is_dir(self::$custom_path)) {
                                wp_mkdir_p(self::$custom_path);
                            }
                            add_filter('acf/settings/save_json', function() {
                                return self::$custom_path;
                            });
                            return;
                        }
                    }
                }
            }
        }
    }

    /**
     * Scan custom JSON files to find which options pages have field groups
     *
     * @return array Associative array of page slugs that have matching field groups.
     */
    public static function get_targeted_pages() {
        if (!is_dir(self::$custom_path)) {
            return array();
        }

        $json_files = glob(self::$custom_path . '/*.json');
        if (empty($json_files)) {
            return array();
        }

        $targeted = array();
        foreach ($json_files as $file) {
            $content = file_get_contents($file);
            $group = json_decode($content, true);

            if (!empty($group['location'])) {
                foreach ($group['location'] as $rule_group) {
                    foreach ($rule_group as $rule) {
                        if (isset($rule['param']) && $rule['param'] === 'options_page' && isset($rule['value'])) {
                            $targeted[$rule['value']] = true;
                        }
                    }
                }
            }
        }

        return $targeted;
    }
}
