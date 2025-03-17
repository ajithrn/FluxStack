<?php
/**
 * Publications Module
 *
 * @package FluxStack
 */

class FS_Publications {
    const ACF_GROUP_ID = 'group_publications_meta_fields';
    private static $acf_json_path;
    
    // Publication type options
    public static $publication_types = array(
        'newsletters' => 'Newsletters',
        'benefits-handbook' => 'Benefits Handbook',
        'informational-pamphlets' => 'Informational Pamphlets',
        'actuarial-valuations' => 'Actuarial Valuations',
        'annual-reports' => 'Annual Reports'
    );

    public static function init() {
        self::$acf_json_path = get_stylesheet_directory() . '/modules/publications/acf-json';
        
        add_action('init', array(__CLASS__, 'register_post_type'), 0);
        add_filter('acf/settings/load_json', array(__CLASS__, 'add_acf_json_load_point'));
        add_action('acf/update_field_group', array(__CLASS__, 'update_field_group'), 1, 1);
        
        // Add admin columns
        add_filter('manage_publication_posts_columns', array(__CLASS__, 'set_custom_columns'));
        add_action('manage_publication_posts_custom_column', array(__CLASS__, 'custom_column_content'), 10, 2);
        add_filter('manage_edit-publication_sortable_columns', array(__CLASS__, 'set_sortable_columns'));
        
        // Add filter for publication type in admin
        add_action('restrict_manage_posts', array(__CLASS__, 'add_admin_filters'));
        add_filter('parse_query', array(__CLASS__, 'filter_publications_by_type'));
        
        // Set default sort order
        add_action('pre_get_posts', array(__CLASS__, 'set_default_sort_order'));
    }

    /**
     * Set default sort order for publications in admin
     */
    public static function set_default_sort_order($query) {
        if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'publication') {
            return;
        }

        // If no orderby is set, default to publication date
        if (!$query->get('orderby')) {
            $query->set('orderby', 'meta_value');
            $query->set('meta_key', 'publication_date');
            $query->set('order', 'DESC');
        }
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
        
        foreach (self::$publication_types as $value => $label) {
            printf(
                '<option value="%s"%s>%s</option>',
                esc_attr($value),
                $selected === $value ? ' selected="selected"' : '',
                esc_html($label)
            );
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
            
            $query->query_vars['meta_key'] = 'publication_type';
            $query->query_vars['meta_value'] = $_GET['publication_type'];
        }
    }

    public static function set_custom_columns($columns) {
        $new_columns = array();
        foreach($columns as $key => $value) {
            if($key === 'title') {
                $new_columns[$key] = $value;
                $new_columns['publication_type'] = __('Publication Type', 'fluxstack');
            } else if($key === 'date') {
                $new_columns['publication_date'] = __('Publication Date', 'fluxstack');
                $new_columns[$key] = $value;
                $new_columns['pdf_file'] = __('PDF File', 'fluxstack');
                $new_columns['thumbnail'] = __('Thumbnail', 'fluxstack');
            } else {
                $new_columns[$key] = $value;
            }
        }
        return $new_columns;
    }

    public static function custom_column_content($column, $post_id) {
        switch($column) {
            case 'publication_type':
                $type = get_field('publication_type', $post_id);
                if($type && isset(self::$publication_types[$type])) {
                    echo '<a href="edit.php?post_type=publication&publication_type=' . esc_attr($type) . '">' . 
                         esc_html(self::$publication_types[$type]) . '</a>';
                } else {
                    echo '—';
                }
                break;
                
            case 'publication_date':
                $date = get_field('publication_date', $post_id);
                if($date) {
                    echo esc_html(date_i18n(get_option('date_format'), strtotime($date)));
                } else {
                    echo '—';
                }
                break;
                
            case 'pdf_file':
                $file = get_field('publication_file', $post_id);
                if($file) {
                    echo '<a href="' . esc_url($file['url']) . '" target="_blank">Download</a>';
                } else {
                    echo '—';
                }
                break;
                
            case 'thumbnail':
                if(has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, array(50, 50));
                } else {
                    echo '—';
                }
                break;
        }
    }

    public static function set_sortable_columns($columns) {
        $columns['publication_date'] = 'publication_date';
        $columns['publication_type'] = 'publication_type';
        return $columns;
    }

    public static function register_post_type() {
        $labels = array(
            'name'                => _x('Publications', 'Post Type General Name', 'fluxstack'),
            'singular_name'       => _x('Publication', 'Post Type Singular Name', 'fluxstack'),
            'menu_name'           => __('Publications', 'fluxstack'),
            'name_admin_bar'      => __('Publication', 'fluxstack'),
            'parent_item_colon'   => __('Parent Publication:', 'fluxstack'),
            'all_items'           => __('All Publications', 'fluxstack'),
            'add_new_item'        => __('Add New Publication', 'fluxstack'),
            'add_new'             => __('Add New', 'fluxstack'),
            'new_item'            => __('New Publication', 'fluxstack'),
            'edit_item'           => __('Edit Publication', 'fluxstack'),
            'update_item'         => __('Update Publication', 'fluxstack'),
            'view_item'           => __('View Publication', 'fluxstack'),
            'search_items'        => __('Search Publication', 'fluxstack'),
            'not_found'           => __('Not found', 'fluxstack'),
            'not_found_in_trash'  => __('Not found in Trash', 'fluxstack'),
        );
        $args = array(
            'label'               => __('publication', 'fluxstack'),
            'description'         => __('Publications', 'fluxstack'),
            'labels'              => $labels,
            'supports'            => array('title', 'thumbnail', 'custom-fields'),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 20,
            'menu_icon'           => 'dashicons-media-document',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
            'show_in_rest'        => false,
            'rewrite'            => array('slug' => 'publication')
        );
        register_post_type('publication', $args);
    }

    /**
     * Get publications by type
     *
     * @param string $type Publication type
     * @param int $posts_per_page Number of posts to return (-1 for all)
     * @param string $orderby Field to order by
     * @param string $order Order direction (ASC or DESC)
     * @return WP_Query Query result
     */
    public static function get_publications_by_type($type, $posts_per_page = -1, $orderby = 'publication_date', $order = 'DESC') {
        $args = array(
            'post_type' => 'publication',
            'posts_per_page' => $posts_per_page,
            'meta_query' => array(
                array(
                    'key' => 'publication_type',
                    'value' => $type,
                    'compare' => '='
                )
            )
        );
        
        if ($orderby === 'publication_date') {
            $args['meta_key'] = 'publication_date';
            $args['orderby'] = 'meta_value';
        } else {
            $args['orderby'] = $orderby;
        }
        
        $args['order'] = $order;
        
        return new WP_Query($args);
    }
    
    /**
     * Get publications by year
     *
     * @param int $year Year to filter by
     * @param string $type Optional publication type
     * @param int $posts_per_page Number of posts to return (-1 for all)
     * @return WP_Query Query result
     */
    public static function get_publications_by_year($year, $type = '', $posts_per_page = -1) {
        $args = array(
            'post_type' => 'publication',
            'posts_per_page' => $posts_per_page,
            'meta_query' => array(
                array(
                    'key' => 'publication_date',
                    'value' => array($year . '-01-01', $year . '-12-31'),
                    'compare' => 'BETWEEN',
                    'type' => 'DATE'
                )
            ),
            'meta_key' => 'publication_date',
            'orderby' => 'meta_value',
            'order' => 'DESC'
        );
        
        if (!empty($type)) {
            $args['meta_query'][] = array(
                'key' => 'publication_type',
                'value' => $type,
                'compare' => '='
            );
        }
        
        return new WP_Query($args);
    }

    public static function add_acf_json_load_point($paths) {
        $paths[] = self::$acf_json_path;
        return $paths;
    }

    public static function update_field_group($group) {
        if ($group['key'] === self::ACF_GROUP_ID) {
            add_filter('acf/settings/save_json', function() {
                return self::$acf_json_path;
            });
        }
    }
}

// Initialize the publications module
FS_Publications::init();
