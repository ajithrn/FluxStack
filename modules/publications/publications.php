<?php
/**
 * Publications Module
 *
 * @package FluxStack
 */

class FS_Publications {
    const ACF_GROUP_ID = 'group_publications_meta_fields';
    private static $acf_json_path;
    const TAXONOMY_NAME = 'publication_type';
    
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
        add_action('init', array(__CLASS__, 'register_taxonomy'), 0);
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
        
        // Sync ACF field with taxonomy
        add_action('acf/save_post', array(__CLASS__, 'sync_acf_with_taxonomy'), 20);
        
        // Hide taxonomy metabox
        add_action('admin_menu', array(__CLASS__, 'remove_taxonomy_metabox'));
        
        // Dynamically populate ACF field choices from taxonomy terms
        add_filter('acf/load_field/key=field_publication_type', array(__CLASS__, 'load_publication_type_choices'));
    }

    /**
     * Register the publication_type taxonomy
     */
    public static function register_taxonomy() {
        $labels = array(
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
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => false, // We'll use our custom column instead
            'query_var'         => true,
            'rewrite'           => array('slug' => 'publication-type'),
            'show_in_rest'      => false,
        );

        register_taxonomy(self::TAXONOMY_NAME, array('publication'), $args);
        
        // Register the taxonomy terms based on our publication types
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
        // Only run on publication post type
        if (get_post_type($post_id) !== 'publication') {
            return;
        }
        
        // Get the ACF field value
        $publication_type = get_field('publication_type', $post_id);
        
        if (!empty($publication_type)) {
            // Set the taxonomy term based on the ACF field value
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

        // If no orderby is set, default to publication date
        if (!$query->get('orderby')) {
            $query->set('orderby', 'meta_value');
            $query->set('meta_key', 'publication_date');
            $query->set('order', 'DESC');
        }
    }

    /**
     * Dynamically populate ACF field choices from taxonomy terms
     *
     * @param array $field The ACF field being loaded
     * @return array The modified field with updated choices
     */
    public static function load_publication_type_choices($field) {
        // Start with an empty choices array
        $field['choices'] = array();
        
        // Get all terms from the taxonomy
        $terms = get_terms(array(
            'taxonomy' => self::TAXONOMY_NAME,
            'hide_empty' => false,
        ));
        
        // Add each term to the choices array
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
        
        // Get all terms from the taxonomy
        $terms = get_terms(array(
            'taxonomy' => self::TAXONOMY_NAME,
            'hide_empty' => false,
        ));
        
        // Add each term to the dropdown
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
            
            // Use taxonomy query instead of meta query
            $query->query_vars['tax_query'] = array(
                array(
                    'taxonomy' => self::TAXONOMY_NAME,
                    'field'    => 'slug',
                    'terms'    => $_GET['publication_type'],
                )
            );
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
                if($type) {
                    // Get the term name from the taxonomy
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
            'tax_query' => array(
                array(
                    'taxonomy' => self::TAXONOMY_NAME,
                    'field'    => 'slug',
                    'terms'    => $type,
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
            $args['tax_query'] = array(
                array(
                    'taxonomy' => self::TAXONOMY_NAME,
                    'field'    => 'slug',
                    'terms'    => $type,
                )
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
