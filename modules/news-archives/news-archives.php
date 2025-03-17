<?php
/**
 * News Archives Module
 *
 * @package FluxStack
 */

class FS_News_Archives {
    /**
     * Taxonomy name for news years
     */
    const YEAR_TAXONOMY = 'news_year';
    
    /**
     * Initialize the module
     */
    public static function init() {
        // Register the year taxonomy
        add_action('init', array(__CLASS__, 'register_year_taxonomy'), 0);
        
        // Auto-assign posts to year terms when published
        add_action('save_post', array(__CLASS__, 'assign_post_to_year'), 10, 3);
        
        // Add filter to admin post list
        add_action('restrict_manage_posts', array(__CLASS__, 'add_year_filter_to_admin'));
        
        // Add year column to admin post list
        add_filter('manage_posts_columns', array(__CLASS__, 'add_year_column'));
        add_action('manage_posts_custom_column', array(__CLASS__, 'display_year_column'), 10, 2);
        
        // Make year column sortable
        add_filter('manage_edit-post_sortable_columns', array(__CLASS__, 'make_year_column_sortable'));
        
        // Add widget for browsing by year
        add_action('widgets_init', array(__CLASS__, 'register_year_widget'));
    }
    
    /**
     * Register the year taxonomy
     */
    public static function register_year_taxonomy() {
        $labels = array(
            'name'              => _x('News Years', 'taxonomy general name', 'fluxstack'),
            'singular_name'     => _x('News Year', 'taxonomy singular name', 'fluxstack'),
            'search_items'      => __('Search Years', 'fluxstack'),
            'all_items'         => __('All Years', 'fluxstack'),
            'parent_item'       => __('Parent Year', 'fluxstack'),
            'parent_item_colon' => __('Parent Year:', 'fluxstack'),
            'edit_item'         => __('Edit Year', 'fluxstack'),
            'update_item'       => __('Update Year', 'fluxstack'),
            'add_new_item'      => __('Add New Year', 'fluxstack'),
            'new_item_name'     => __('New Year Name', 'fluxstack'),
            'menu_name'         => __('Years', 'fluxstack'),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'news/year'),
            'show_in_rest'      => true,
        );

        register_taxonomy(self::YEAR_TAXONOMY, array('post'), $args);
        
        // Create default year terms if they don't exist
        self::create_default_year_terms();
    }
    
    /**
     * Create default year terms
     */
    private static function create_default_year_terms() {
        $current_year = date('Y');
        $start_year = 2011; // Based on the screenshot showing archives back to 2011
        
        for ($year = $start_year; $year <= $current_year; $year++) {
            if (!term_exists($year, self::YEAR_TAXONOMY)) {
                wp_insert_term($year, self::YEAR_TAXONOMY, array(
                    'slug' => $year,
                    'description' => sprintf(__('News from %s', 'fluxstack'), $year)
                ));
            }
        }
    }
    
    /**
     * Assign post to the appropriate year term when published
     */
    public static function assign_post_to_year($post_id, $post, $update) {
        // Skip if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Skip if this is not a post
        if ($post->post_type !== 'post') {
            return;
        }
        
        // Skip if this is a revision
        if (wp_is_post_revision($post_id)) {
            return;
        }
        
        // Skip if this is not a published post
        if ($post->post_status !== 'publish') {
            return;
        }
        
        // Get the year from the post date
        $year = date('Y', strtotime($post->post_date));
        
        // Check if the year term exists, create it if it doesn't
        if (!term_exists($year, self::YEAR_TAXONOMY)) {
            wp_insert_term($year, self::YEAR_TAXONOMY, array(
                'slug' => $year,
                'description' => sprintf(__('News from %s', 'fluxstack'), $year)
            ));
        }
        
        // Assign the post to the year term
        wp_set_object_terms($post_id, $year, self::YEAR_TAXONOMY, false);
    }
    
    /**
     * Add year filter to admin post list
     */
    public static function add_year_filter_to_admin($post_type) {
        if ($post_type !== 'post') {
            return;
        }
        
        $taxonomy = self::YEAR_TAXONOMY;
        $tax = get_taxonomy($taxonomy);
        $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
        
        wp_dropdown_categories(array(
            'show_option_all' => sprintf(__('All %s', 'fluxstack'), $tax->label),
            'taxonomy' => $taxonomy,
            'name' => $taxonomy,
            'orderby' => 'name',
            'selected' => $selected,
            'hierarchical' => true,
            'show_count' => true,
            'hide_empty' => true,
            'value_field' => 'slug',
        ));
    }
    
    /**
     * Add year column to admin post list
     */
    public static function add_year_column($columns) {
        $new_columns = array();
        
        foreach ($columns as $key => $value) {
            if ($key === 'categories') {
                $new_columns[$key] = $value;
                $new_columns['news_year'] = __('Year', 'fluxstack');
            } else {
                $new_columns[$key] = $value;
            }
        }
        
        return $new_columns;
    }
    
    /**
     * Display year column in admin post list
     */
    public static function display_year_column($column, $post_id) {
        if ($column === 'news_year') {
            $terms = get_the_terms($post_id, self::YEAR_TAXONOMY);
            
            if (!empty($terms)) {
                $output = array();
                
                foreach ($terms as $term) {
                    $output[] = sprintf(
                        '<a href="%s">%s</a>',
                        esc_url(add_query_arg(array(self::YEAR_TAXONOMY => $term->slug), 'edit.php')),
                        esc_html($term->name)
                    );
                }
                
                echo implode(', ', $output);
            } else {
                echo '—';
            }
        }
    }
    
    /**
     * Make year column sortable
     */
    public static function make_year_column_sortable($columns) {
        $columns['news_year'] = self::YEAR_TAXONOMY;
        return $columns;
    }
    
    /**
     * Register year widget
     */
    public static function register_year_widget() {
        register_widget('FS_News_Year_Widget');
    }
    
    /**
     * Get posts by year
     *
     * @param string $year Year to filter by
     * @param int $posts_per_page Number of posts to return (-1 for all)
     * @return WP_Query Query result
     */
    public static function get_posts_by_year($year, $posts_per_page = -1) {
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $posts_per_page,
            'tax_query' => array(
                array(
                    'taxonomy' => self::YEAR_TAXONOMY,
                    'field' => 'slug',
                    'terms' => $year,
                ),
            ),
        );
        
        return new WP_Query($args);
    }
    
    /**
     * Get all years with post counts
     *
     * @param bool $hide_empty Whether to hide years with no posts
     * @return array Array of year terms
     */
    public static function get_years($hide_empty = true) {
        $terms = get_terms(array(
            'taxonomy' => self::YEAR_TAXONOMY,
            'orderby' => 'name',
            'order' => 'DESC',
            'hide_empty' => $hide_empty,
        ));
        
        return $terms;
    }
}

/**
 * News Year Widget
 */
class FS_News_Year_Widget extends WP_Widget {
    /**
     * Register widget with WordPress
     */
    public function __construct() {
        parent::__construct(
            'fs_news_year_widget',
            __('News Years', 'fluxstack'),
            array('description' => __('Display a list of news years', 'fluxstack'))
        );
    }
    
    /**
     * Front-end display of widget
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? apply_filters('widget_title', $instance['title']) : __('News Archives', 'fluxstack');
        
        echo $args['before_widget'];
        
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $years = FS_News_Archives::get_years();
        
        if (!empty($years)) {
            echo '<ul class="news-years-list">';
            
            foreach ($years as $year) {
                printf(
                    '<li><a href="%s">%s</a> <span class="count">(%s)</span></li>',
                    esc_url(get_term_link($year)),
                    esc_html($year->name),
                    esc_html($year->count)
                );
            }
            
            echo '</ul>';
        }
        
        echo $args['after_widget'];
    }
    
    /**
     * Back-end widget form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('News Archives', 'fluxstack');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'fluxstack'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }
    
    /**
     * Sanitize widget form values as they are saved
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        
        return $instance;
    }
}

// Initialize the news archives module
FS_News_Archives::init();
