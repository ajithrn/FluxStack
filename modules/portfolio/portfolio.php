<?php
/**
 * Portfolio Module
 *
 * Manages the 'portfolio' custom post type for project showcase.
 *
 * @package FluxStack
 */

class FS_Portfolio extends FS_Base_CPT_Module {
    protected static function get_module_dir() {
        return 'portfolio';
    }

    protected static function get_acf_group_id() {
        return 'group_portfolio_meta_fields';
    }

    protected static function get_post_type_config() {
        return array(
            'post_type'     => 'portfolio',
            'slug'          => 'portfolio',
            'menu_icon'     => 'dashicons-portfolio',
            'menu_position' => 20,
            'supports'      => array('title', 'thumbnail', 'excerpt', 'revisions', 'page-attributes'),
            'labels'        => array(
                'name'               => _x('Portfolio', 'post type general name', 'fluxstack'),
                'singular_name'      => _x('Portfolio Item', 'post type singular name', 'fluxstack'),
                'menu_name'          => _x('Portfolio', 'admin menu', 'fluxstack'),
                'name_admin_bar'     => _x('Portfolio', 'add new on admin bar', 'fluxstack'),
                'add_new'            => _x('Add New', 'portfolio item', 'fluxstack'),
                'add_new_item'       => __('Add New Portfolio', 'fluxstack'),
                'new_item'           => __('New Portfolio Item', 'fluxstack'),
                'edit_item'          => __('Edit Portfolio Item', 'fluxstack'),
                'view_item'          => __('View Portfolio Item', 'fluxstack'),
                'all_items'          => __('All Portfolio', 'fluxstack'),
                'search_items'       => __('Search Portfolio Items', 'fluxstack'),
                'parent_item_colon'  => __('Parent Portfolio Items:', 'fluxstack'),
                'not_found'          => __('No portfolio items found.', 'fluxstack'),
                'not_found_in_trash' => __('No portfolio items found in Trash.', 'fluxstack'),
            ),
        );
    }

    protected static function get_taxonomy_config() {
        return array(
            array(
                'taxonomy' => 'portfolio_type',
                'slug'     => 'portfolio-type',
                'labels'   => array(
                    'name'              => _x('Portfolio Types', 'taxonomy general name', 'fluxstack'),
                    'singular_name'     => _x('Portfolio Type', 'taxonomy singular name', 'fluxstack'),
                    'search_items'      => __('Search Portfolio Types', 'fluxstack'),
                    'all_items'         => __('All Portfolio Types', 'fluxstack'),
                    'parent_item'       => __('Parent Portfolio Type', 'fluxstack'),
                    'parent_item_colon' => __('Parent Portfolio Type:', 'fluxstack'),
                    'edit_item'         => __('Edit Portfolio Type', 'fluxstack'),
                    'update_item'       => __('Update Portfolio Type', 'fluxstack'),
                    'add_new_item'      => __('Add New Portfolio Type', 'fluxstack'),
                    'new_item_name'     => __('New Portfolio Type Name', 'fluxstack'),
                    'menu_name'         => __('Portfolio Types', 'fluxstack'),
                ),
                'args' => array(
                    'show_in_rest' => true,
                ),
            ),
        );
    }

    protected static function get_custom_columns() {
        return array(
            'thumbnail'      => __('Thumbnail', 'fluxstack'),
            'client'         => __('Client', 'fluxstack'),
            'year'           => __('Year', 'fluxstack'),
            'portfolio_type' => __('Type', 'fluxstack'),
        );
    }

    protected static function get_sortable_columns() {
        return array(
            'client'         => 'client',
            'year'           => 'year',
            'portfolio_type' => 'portfolio_type',
        );
    }

    public static function render_column($column, $post_id) {
        switch ($column) {
            case 'client':
                $client = get_field('basic_info_client', $post_id);
                $website = get_field('basic_info_website', $post_id);
                if ($client) {
                    echo '<strong>' . esc_html($client) . '</strong>';
                    if ($website) {
                        echo '<br><a href="' . esc_url($website) . '" target="_blank">';
                        echo '<small>' . esc_url($website) . '</small>';
                        echo '</a>';
                    }
                } else {
                    echo '—';
                }
                break;

            case 'year':
                $year = get_field('basic_info_year', $post_id);
                echo $year ? esc_html($year) : '—';
                break;

            case 'portfolio_type':
                $terms = get_the_terms($post_id, 'portfolio_type');
                if (!empty($terms) && !is_wp_error($terms)) {
                    $term_links = array();
                    foreach ($terms as $term) {
                        $term_links[] = sprintf(
                            '<a href="%s">%s</a>',
                            esc_url(add_query_arg(array('post_type' => 'portfolio', 'portfolio_type' => $term->slug), 'edit.php')),
                            esc_html($term->name)
                        );
                    }
                    echo implode(', ', $term_links);
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
        if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'portfolio') {
            return;
        }

        $orderby = $query->get('orderby');

        switch ($orderby) {
            case 'client':
                $query->set('meta_key', 'basic_info_client');
                $query->set('orderby', 'meta_value');
                break;
            case 'year':
                $query->set('meta_key', 'basic_info_year');
                $query->set('orderby', 'meta_value_num');
                break;
        }
    }

    /**
     * Get portfolio items
     *
     * @param array $args Query arguments.
     * @return WP_Query
     */
    public static function get_portfolio_items($args = array()) {
        return self::get_items($args);
    }

    /**
     * Get portfolio items by type
     *
     * @param string|array $type Portfolio type slug(s).
     * @param int $limit Number of items to return.
     * @return WP_Query
     */
    public static function get_portfolio_by_type($type, $limit = -1) {
        return self::get_items(array(
            'tax_query' => array(
                array(
                    'taxonomy' => 'portfolio_type',
                    'field'    => 'slug',
                    'terms'    => $type,
                ),
            ),
            'posts_per_page' => $limit,
        ));
    }

    /**
     * Get portfolio items by year
     *
     * @param int $year Year to filter by.
     * @param int $limit Number of items to return.
     * @return WP_Query
     */
    public static function get_portfolio_by_year($year, $limit = -1) {
        return self::get_items(array(
            'meta_query' => array(
                array(
                    'key'   => 'basic_info_year',
                    'value' => $year,
                ),
            ),
            'posts_per_page' => $limit,
        ));
    }
}
