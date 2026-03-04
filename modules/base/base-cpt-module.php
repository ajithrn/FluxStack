<?php
/**
 * Base CPT Module
 *
 * Abstract base class for content post type modules.
 * Handles common functionality: CPT registration, taxonomy registration,
 * ACF JSON save/load points, and admin column management.
 *
 * @package FluxStack
 */

abstract class FS_Base_CPT_Module {
    /**
     * Get the post type configuration.
     *
     * @return array {
     *     @type string $post_type    Post type slug.
     *     @type string $slug         URL rewrite slug.
     *     @type string $menu_icon    Dashicon class.
     *     @type int    $menu_position Menu position.
     *     @type array  $supports     Post type supports.
     *     @type array  $labels       Post type labels.
     *     @type array  $args         Additional register_post_type args (optional overrides).
     * }
     */
    abstract protected static function get_post_type_config();

    /**
     * Get the module directory name (used for ACF JSON path).
     *
     * @return string Module directory name (e.g., 'services', 'teams').
     */
    abstract protected static function get_module_dir();

    /**
     * ACF field group ID for this module.
     * Override in child classes if needed.
     *
     * @return string|null ACF group ID or null if not applicable.
     */
    protected static function get_acf_group_id() {
        return null;
    }

    /**
     * Get taxonomy configurations.
     * Override in child classes to register taxonomies.
     *
     * @return array Array of taxonomy configs, each with:
     *     @type string $taxonomy  Taxonomy slug.
     *     @type string $slug      Rewrite slug.
     *     @type array  $labels    Taxonomy labels.
     *     @type array  $args      Additional register_taxonomy args (optional overrides).
     */
    protected static function get_taxonomy_config() {
        return array();
    }

    /**
     * Get custom admin columns.
     * Override in child classes to add custom columns.
     *
     * @return array Associative array of column_id => column_label.
     */
    protected static function get_custom_columns() {
        return array();
    }

    /**
     * Get sortable admin columns.
     * Override in child classes to define sortable columns.
     *
     * @return array Associative array of column_id => orderby_value.
     */
    protected static function get_sortable_columns() {
        return array();
    }

    /**
     * Render a custom admin column value.
     * Override in child classes to render column content.
     *
     * @param string $column  Column identifier.
     * @param int    $post_id Post ID.
     */
    public static function render_column($column, $post_id) {
        // Default: render thumbnail column
        if ($column === 'thumbnail') {
            if (has_post_thumbnail($post_id)) {
                echo '<a href="' . esc_url(get_edit_post_link($post_id)) . '">';
                echo get_the_post_thumbnail($post_id, array(60, 60));
                echo '</a>';
            } else {
                $config = static::get_post_type_config();
                $icon = isset($config['menu_icon']) ? $config['menu_icon'] : 'dashicons-admin-post';
                echo '<div class="fluxstack-thumbnail-placeholder">';
                echo '<span class="dashicons ' . esc_attr($icon) . '"></span>';
                echo '</div>';
            }
        }
    }

    /**
     * Handle admin column sorting.
     * Override in child classes for custom sort logic.
     *
     * @param WP_Query $query The main query.
     */
    public static function handle_column_sorting($query) {
        // Override in child classes
    }

    /**
     * Initialize the module.
     * Registers all hooks for CPT, taxonomy, ACF, and admin columns.
     */
    public static function init() {
        $config = static::get_post_type_config();
        $post_type = $config['post_type'];

        // Register post type
        add_action('init', array(static::class, 'register_post_type'), 0);

        // Register taxonomies
        $taxonomies = static::get_taxonomy_config();
        if (!empty($taxonomies)) {
            add_action('init', array(static::class, 'register_taxonomies'), 0);
        }

        // ACF JSON load/save points
        $module_dir = static::get_module_dir();
        $acf_json_path = get_stylesheet_directory() . '/modules/' . $module_dir . '/acf-json';

        if (is_dir($acf_json_path)) {
            add_filter('acf/settings/load_json', function($paths) use ($acf_json_path) {
                $paths[] = $acf_json_path;
                return $paths;
            });

            $acf_group_id = static::get_acf_group_id();
            if ($acf_group_id) {
                add_action('acf/update_field_group', function($group) use ($acf_group_id, $acf_json_path) {
                    if ($group['key'] === $acf_group_id) {
                        add_filter('acf/settings/save_json', function() use ($acf_json_path) {
                            return $acf_json_path;
                        });
                    }
                }, 1, 1);
            }
        }

        // Admin columns
        $custom_columns = static::get_custom_columns();
        if (!empty($custom_columns)) {
            add_filter("manage_{$post_type}_posts_columns", array(static::class, 'add_admin_columns'));
            add_action("manage_{$post_type}_posts_custom_column", array(static::class, 'render_admin_columns'), 10, 2);

            $sortable = static::get_sortable_columns();
            if (!empty($sortable)) {
                add_filter("manage_edit-{$post_type}_sortable_columns", array(static::class, 'register_sortable_columns'));
                add_action('pre_get_posts', array(static::class, 'handle_column_sorting'));
            }
        }

        // Allow child classes to add additional hooks
        static::register_hooks();
    }

    /**
     * Register additional hooks.
     * Override in child classes to add module-specific hooks.
     */
    protected static function register_hooks() {
        // Override in child classes
    }

    /**
     * Register the post type.
     */
    public static function register_post_type() {
        $config = static::get_post_type_config();

        $default_args = array(
            'labels'             => $config['labels'],
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => $config['slug']),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => isset($config['menu_position']) ? $config['menu_position'] : 20,
            'menu_icon'          => isset($config['menu_icon']) ? $config['menu_icon'] : 'dashicons-admin-post',
            'supports'           => isset($config['supports']) ? $config['supports'] : array('title', 'thumbnail'),
            'show_in_rest'       => false,
        );

        // Merge with any additional args from config
        if (isset($config['args'])) {
            $default_args = array_merge($default_args, $config['args']);
        }

        register_post_type($config['post_type'], $default_args);
    }

    /**
     * Register taxonomies.
     */
    public static function register_taxonomies() {
        $config = static::get_post_type_config();
        $taxonomies = static::get_taxonomy_config();

        foreach ($taxonomies as $tax_config) {
            $default_args = array(
                'hierarchical'      => true,
                'labels'            => $tax_config['labels'],
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => array('slug' => $tax_config['slug']),
                'show_in_rest'      => false,
            );

            if (isset($tax_config['args'])) {
                $default_args = array_merge($default_args, $tax_config['args']);
            }

            register_taxonomy(
                $tax_config['taxonomy'],
                array($config['post_type']),
                $default_args
            );
        }
    }

    /**
     * Add custom admin columns.
     *
     * @param array $columns Existing columns.
     * @return array Modified columns.
     */
    public static function add_admin_columns($columns) {
        $custom = static::get_custom_columns();
        $new_columns = array();

        // Preserve checkbox
        if (isset($columns['cb'])) {
            $new_columns['cb'] = $columns['cb'];
        }

        // Add thumbnail column if defined
        if (isset($custom['thumbnail'])) {
            $new_columns['thumbnail'] = $custom['thumbnail'];
            unset($custom['thumbnail']);
        }

        // Add title
        if (isset($columns['title'])) {
            $new_columns['title'] = $columns['title'];
        }

        // Add custom columns
        foreach ($custom as $key => $label) {
            $new_columns[$key] = $label;
        }

        // Add date
        if (isset($columns['date'])) {
            $new_columns['date'] = $columns['date'];
        }

        return $new_columns;
    }

    /**
     * Render admin column content (dispatcher).
     *
     * @param string $column  Column identifier.
     * @param int    $post_id Post ID.
     */
    public static function render_admin_columns($column, $post_id) {
        static::render_column($column, $post_id);
    }

    /**
     * Register sortable columns.
     *
     * @param array $columns Existing sortable columns.
     * @return array Modified sortable columns.
     */
    public static function register_sortable_columns($columns) {
        $sortable = static::get_sortable_columns();
        return array_merge($columns, $sortable);
    }

    /**
     * Get items query helper.
     *
     * @param array $args Query arguments.
     * @return WP_Query Query result.
     */
    public static function get_items($args = array()) {
        $config = static::get_post_type_config();

        $defaults = array(
            'post_type'      => $config['post_type'],
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        );

        $args = wp_parse_args($args, $defaults);
        return new WP_Query($args);
    }
}
