<?php
/**
 * Publications Module
 *
 * Manages the 'publication' custom post type with taxonomy-based
 * publication types and ACF field synchronization.
 *
 * @package FluxStack
 */

class FS_Publications extends FS_Base_CPT_Module {
    const TAXONOMY_NAME = 'publication_type';

    /**
     * Default publication type terms
     */
    public static $publication_types = array(
        'newsletters'             => 'Newsletters',
        'benefits-handbook'       => 'Benefits Handbook',
        'informational-pamphlets' => 'Informational Pamphlets',
        'actuarial-valuations'    => 'Actuarial Valuations',
        'annual-reports'          => 'Annual Reports',
    );

    protected static function get_module_dir() {
        return 'publications';
    }

    protected static function get_acf_group_id() {
        return 'group_publications_meta_fields';
    }

    protected static function get_post_type_config() {
        return array(
            'post_type'     => 'publication',
            'slug'          => 'publication',
            'menu_icon'     => 'dashicons-media-document',
            'menu_position' => 20,
            'supports'      => array('title', 'thumbnail', 'custom-fields'),
            'labels'        => array(
                'name'               => _x('Publications', 'Post Type General Name', 'fluxstack'),
                'singular_name'      => _x('Publication', 'Post Type Singular Name', 'fluxstack'),
                'menu_name'          => __('Publications', 'fluxstack'),
                'name_admin_bar'     => __('Publication', 'fluxstack'),
                'parent_item_colon'  => __('Parent Publication:', 'fluxstack'),
                'all_items'          => __('All Publications', 'fluxstack'),
                'add_new_item'       => __('Add New Publication', 'fluxstack'),
                'add_new'            => __('Add New', 'fluxstack'),
                'new_item'           => __('New Publication', 'fluxstack'),
                'edit_item'          => __('Edit Publication', 'fluxstack'),
                'update_item'        => __('Update Publication', 'fluxstack'),
                'view_item'          => __('View Publication', 'fluxstack'),
                'search_items'       => __('Search Publication', 'fluxstack'),
                'not_found'          => __('Not found', 'fluxstack'),
                'not_found_in_trash' => __('Not found in Trash', 'fluxstack'),
            ),
            'args' => array(
                'public'              => false,
                'publicly_queryable'  => false,
                'capability_type'     => 'page',
            ),
        );
    }

    protected static function get_taxonomy_config() {
        return array(
            array(
                'taxonomy' => self::TAXONOMY_NAME,
                'slug'     => 'publication-type',
                'labels'   => array(
                    'name'              => _x('Publication Types', 'taxonomy general name', 'fluxstack'),
                    'singular_name'     => _x('Publication Type', 'taxonomy singular name', 'fluxstack'),
                    'search_items'      => __('Search Publication Types', 'fluxstack'),
                    'all_items'         => __('All Publication Types', 'fluxstack'),
                    'parent_item'       => __('Parent Publication Type', 'fluxstack'),
                    'parent_item_colon' => __('Parent Publication Type:', 'fluxstack'),
                    'edit_item'         => __('Edit Publication Type', 'fluxstack'),
                    'update_item'       => __('Update Publication Type', 'fluxstack'),
                    'add_new_item'      => __('Add New Publication Type', 'fluxstack'),
                    'new_item_name'     => __('New Publication Type Name', 'fluxstack'),
                    'menu_name'         => __('Publication Types', 'fluxstack'),
                ),
                'args' => array(
                    'show_admin_column'  => false,
                    'public'             => true,
                    'publicly_queryable' => true,
                ),
            ),
        );
    }

    protected static function get_custom_columns() {
        return array(
            'publication_type' => __('Publication Type', 'fluxstack'),
            'publication_date' => __('Publication Date', 'fluxstack'),
            'pdf_file'         => __('PDF File', 'fluxstack'),
            'thumbnail'        => __('Thumbnail', 'fluxstack'),
        );
    }

    protected static function get_sortable_columns() {
        return array(
            'publication_date' => 'publication_date',
            'publication_type' => 'publication_type',
        );
    }

    /**
     * Register additional hooks specific to publications
     */
    protected static function register_hooks() {
        // Create default taxonomy terms
        add_action('init', array(__CLASS__, 'create_default_terms'), 1);

        // Admin filters
        add_action('restrict_manage_posts', array(__CLASS__, 'add_admin_filters'));
        add_filter('parse_query', array(__CLASS__, 'filter_publications_by_type'));

        // Default sort order
        add_action('pre_get_posts', array(__CLASS__, 'set_default_sort_order'));

        // Sync ACF field with taxonomy
        add_action('acf/save_post', array(__CLASS__, 'sync_acf_with_taxonomy'), 20);

        // Hide taxonomy metabox (managed via ACF)
        add_action('admin_menu', array(__CLASS__, 'remove_taxonomy_metabox'));

        // Dynamic ACF field choices
        add_filter('acf/load_field/key=field_publication_type', array(__CLASS__, 'load_publication_type_choices'));

        // Redirect single publication to archive
        add_action('template_redirect', array(__CLASS__, 'redirect_single_publication'));
    }

    /**
     * Create default publication type terms
     */
    public static function create_default_terms() {
        foreach (self::$publication_types as $slug => $name) {
            if (!term_exists($slug, self::TAXONOMY_NAME)) {
                wp_insert_term($name, self::TAXONOMY_NAME, array('slug' => $slug));
            }
        }
    }

    /**
     * Sync the ACF field value with the taxonomy term
     */
    public static function sync_acf_with_taxonomy($post_id) {
        if (get_post_type($post_id) !== 'publication') {
            return;
        }

        $publication_type = get_field('publication_type', $post_id);
        if (!empty($publication_type)) {
            wp_set_object_terms($post_id, $publication_type, self::TAXONOMY_NAME);
        }
    }

    /**
     * Remove the taxonomy metabox from the edit screen
     */
    public static function remove_taxonomy_metabox() {
        remove_meta_box(self::TAXONOMY_NAME . 'div', 'publication', 'side');
    }

    /**
     * Set default sort order for publications in admin
     */
    public static function set_default_sort_order($query) {
        if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'publication') {
            return;
        }

        if (!$query->get('orderby')) {
            $query->set('orderby', 'meta_value');
            $query->set('meta_key', 'publication_date');
            $query->set('order', 'DESC');
        }
    }

    /**
     * Dynamically populate ACF field choices from taxonomy terms
     */
    public static function load_publication_type_choices($field) {
        $field['choices'] = array();

        $terms = get_terms(array(
            'taxonomy'   => self::TAXONOMY_NAME,
            'hide_empty' => false,
        ));

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $field['choices'][$term->slug] = $term->name;
            }
        }

        return $field;
    }

    /**
     * Add filters to the admin list view
     */
    public static function add_admin_filters($post_type) {
        if ('publication' !== $post_type) {
            return;
        }

        $selected = isset($_GET['publication_type']) ? $_GET['publication_type'] : '';

        echo '<select name="publication_type">';
        echo '<option value="">' . __('All Publication Types', 'fluxstack') . '</option>';

        $terms = get_terms(array(
            'taxonomy'   => self::TAXONOMY_NAME,
            'hide_empty' => false,
        ));

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                printf(
                    '<option value="%s"%s>%s</option>',
                    esc_attr($term->slug),
                    $selected === $term->slug ? ' selected="selected"' : '',
                    esc_html($term->name)
                );
            }
        }

        echo '</select>';
    }

    /**
     * Filter publications by type in admin
     */
    public static function filter_publications_by_type($query) {
        global $pagenow;

        if ($pagenow == 'edit.php' &&
            isset($query->query_vars['post_type']) &&
            $query->query_vars['post_type'] == 'publication' &&
            isset($_GET['publication_type']) &&
            !empty($_GET['publication_type'])) {

            $query->query_vars['tax_query'] = array(
                array(
                    'taxonomy' => self::TAXONOMY_NAME,
                    'field'    => 'slug',
                    'terms'    => $_GET['publication_type'],
                )
            );
        }
    }

    public static function render_column($column, $post_id) {
        switch ($column) {
            case 'publication_type':
                $type = get_field('publication_type', $post_id);
                if ($type) {
                    $term = get_term_by('slug', $type, self::TAXONOMY_NAME);
                    if ($term && !is_wp_error($term)) {
                        echo '<a href="edit.php?post_type=publication&publication_type=' . esc_attr($type) . '">' .
                             esc_html($term->name) . '</a>';
                    } else {
                        echo esc_html($type);
                    }
                } else {
                    echo '—';
                }
                break;

            case 'publication_date':
                $date = get_field('publication_date', $post_id);
                echo $date ? esc_html(date_i18n(get_option('date_format'), strtotime($date))) : '—';
                break;

            case 'pdf_file':
                $file = get_field('publication_file', $post_id);
                if ($file) {
                    echo '<a href="' . esc_url($file['url']) . '" target="_blank">Download</a>';
                } else {
                    echo '—';
                }
                break;

            default:
                parent::render_column($column, $post_id);
                break;
        }
    }

    public static function handle_column_sorting($query) {
        if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'publication') {
            return;
        }

        if ($query->get('orderby') === 'publication_date') {
            $query->set('meta_key', 'publication_date');
            $query->set('orderby', 'meta_value');
        }
    }

    /**
     * Redirect single publication posts to their type archive
     */
    public static function redirect_single_publication() {
        if (is_singular('publication')) {
            $post_id = get_the_ID();
            $publication_type = get_field('publication_type', $post_id);

            if (!empty($publication_type)) {
                $term = get_term_by('slug', $publication_type, self::TAXONOMY_NAME);
                if ($term && !is_wp_error($term)) {
                    $redirect_url = get_term_link($term, self::TAXONOMY_NAME);
                    wp_redirect($redirect_url, 301);
                    exit;
                }
            }

            wp_redirect(get_post_type_archive_link('publication'), 301);
            exit;
        }
    }

    /**
     * Get publications by type
     *
     * @param string $type Publication type slug.
     * @param int    $posts_per_page Number of posts (-1 for all).
     * @param string $orderby Field to order by.
     * @param string $order Order direction.
     * @return WP_Query
     */
    public static function get_publications_by_type($type, $posts_per_page = -1, $orderby = 'publication_date', $order = 'DESC') {
        $args = array(
            'tax_query' => array(
                array(
                    'taxonomy' => self::TAXONOMY_NAME,
                    'field'    => 'slug',
                    'terms'    => $type,
                )
            ),
            'posts_per_page' => $posts_per_page,
            'order'          => $order,
        );

        if ($orderby === 'publication_date') {
            $args['meta_key'] = 'publication_date';
            $args['orderby']  = 'meta_value';
        } else {
            $args['orderby'] = $orderby;
        }

        return self::get_items($args);
    }

    /**
     * Get publications by year
     *
     * @param int    $year Year to filter by.
     * @param string $type Optional publication type.
     * @param int    $posts_per_page Number of posts (-1 for all).
     * @return WP_Query
     */
    public static function get_publications_by_year($year, $type = '', $posts_per_page = -1) {
        $args = array(
            'meta_query' => array(
                array(
                    'key'     => 'publication_date',
                    'value'   => array($year . '-01-01', $year . '-12-31'),
                    'compare' => 'BETWEEN',
                    'type'    => 'DATE',
                )
            ),
            'meta_key'       => 'publication_date',
            'orderby'        => 'meta_value',
            'order'          => 'DESC',
            'posts_per_page' => $posts_per_page,
        );

        if (!empty($type)) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => self::TAXONOMY_NAME,
                    'field'    => 'slug',
                    'terms'    => $type,
                )
            );
        }

        return self::get_items($args);
    }
}
