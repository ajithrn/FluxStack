<?php
/**
 * Module Manager
 *
 * @package FluxStack
 */

class FS_Module_Manager {
    // Option names for storing settings
    const OPTION_NAME = 'fluxstack_module_settings';
    const BLOCK_OPTION_NAME = 'fluxstack_block_settings';
    
    // Default modules configuration
    private static $default_modules = array(
        'bricks' => true,
        'dynamic-snippets' => true,
        'image-gallery' => true,
        'news-archives' => true,
        'portfolio' => true,
        'publications' => true,
        'services' => true,
        'teams' => true,
        'testimonials' => true,
        'theme-options' => true,
        'utility-functions' => true,
        'white-label' => true,
    );
    
    // Module groups for UI organization
    private static $module_groups = array(
        'core' => array(
            'title' => 'Core Modules',
            'modules' => array('bricks', 'theme-options', 'utility-functions')
        ),
        'content' => array(
            'title' => 'Content Modules',
            'modules' => array('teams', 'publications', 'testimonials', 'image-gallery', 'news-archives', 'portfolio', 'services')
        ),
        'customization' => array(
            'title' => 'Customization Modules',
            'modules' => array('dynamic-snippets', 'white-label')
        )
    );
    
    // Module descriptions
    private static $module_descriptions = array(
        'bricks' => 'Bricks Builder customizations and extensions',
        'dynamic-snippets' => 'Reusable code snippets and components',
        'image-gallery' => 'Image gallery management with categories',
        'news-archives' => 'Year-based news organization and archives',
        'portfolio' => 'Portfolio management system with project details and gallery',
        'publications' => 'Publication management system with types and PDF support',
        'services' => 'Services management system with intro text and content sections',
        'teams' => 'Team member management with profiles and categories',
        'testimonials' => 'Testimonial management with ratings and categories',
        'theme-options' => 'Theme settings and customization options',
        'utility-functions' => 'Helper functions used by other modules',
        'white-label' => 'Admin interface customization and branding'
    );
    
    /**
     * Module dependencies
     * Key: Module that has dependencies
     * Value: Array of modules it depends on
     */
    private static $module_dependencies = array(
        'bricks' => array(),  // No dependencies
        'dynamic-snippets' => array('utility-functions'),
        'image-gallery' => array('utility-functions'),
        'news-archives' => array('utility-functions'),
        'portfolio' => array('utility-functions'),
        'publications' => array('utility-functions'),
        'services' => array('utility-functions'),
        'teams' => array('utility-functions'),
        'testimonials' => array('utility-functions'),
        'theme-options' => array('utility-functions'),
        'white-label' => array('utility-functions'),
    );
    
    /**
     * Default blocks configuration
     */
    private static $default_blocks = array(
        'columns-25-75' => true,
        'button-styles' => true,
    );
    
    /**
     * Block descriptions
     */
    private static $block_descriptions = array(
        'columns-25-75' => 'A two-column layout with 25/75 ratio and 1.5em gap',
        'button-styles' => 'Custom button styles for the block editor',
    );
    
    /**
     * Block module dependencies
     * Key: Block that has dependencies
     * Value: Array of modules it depends on
     */
    private static $block_module_dependencies = array(
        'columns-25-75' => array(), // No module dependencies
        'button-styles' => array(), // No module dependencies
    );
    
    /**
     * Block dependencies
     * Key: Block that has dependencies
     * Value: Array of blocks it depends on
     */
    private static $block_dependencies = array(
        'columns-25-75' => array(), // No block dependencies
        'button-styles' => array(), // No block dependencies
    );
    
    /**
     * Initialize the module manager
     */
    public static function init() {
        // Add admin menu
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
        
        // Register settings
        add_action('admin_init', array(__CLASS__, 'register_settings'));
        
        // Add settings link on themes page
        add_filter('theme_action_links_fluxstack', array(__CLASS__, 'add_settings_link'));
        
        // Enqueue admin scripts and styles
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_assets'));
        
        // Handle reset to defaults
        add_action('admin_init', array(__CLASS__, 'handle_reset_defaults'));
    }
    
    /**
     * Add admin menu item
     */
    public static function add_admin_menu() {
        add_theme_page(
            __('FluxStack Settings', 'fluxstack'),
            __('FluxStack Settings', 'fluxstack'),
            'manage_options',
            'fluxstack-settings',
            array(__CLASS__, 'render_settings_page')
        );
    }
    
    /**
     * Register settings
     */
    public static function register_settings() {
        // Register module settings
        register_setting(
            'fluxstack_settings',
            self::OPTION_NAME,
            array(__CLASS__, 'sanitize_settings')
        );
        
        // Register block settings
        register_setting(
            'fluxstack_settings',
            self::BLOCK_OPTION_NAME,
            array(__CLASS__, 'sanitize_block_settings')
        );
    }
    
    /**
     * Sanitize settings
     */
    public static function sanitize_settings($input) {
        // Check if the form was submitted (to handle case when all checkboxes are unchecked)
        $was_submitted = isset($_POST[self::OPTION_NAME . '_submitted']);
        
        // If form was submitted but input is empty, it means all checkboxes were unchecked
        if ($was_submitted && empty($input)) {
            $input = array();
        }
        
        $sanitized = array();
        
        // Process each module
        foreach (self::$default_modules as $module => $default) {
            // If form was submitted, use the input value or false if not set
            // Otherwise, use the default value
            $sanitized[$module] = $was_submitted 
                ? (isset($input[$module]) ? (bool) $input[$module] : false)
                : $default;
        }
        
        // Validate dependencies
        $sanitized = self::validate_dependencies($sanitized);
        
        return $sanitized;
    }
    
    /**
     * Validate dependencies before saving settings
     */
    public static function validate_dependencies($input) {
        $validated = $input;
        
        foreach (self::$module_dependencies as $module => $dependencies) {
            // Skip if module is disabled or not in input
            if (!isset($input[$module]) || !$input[$module]) {
                continue;
            }
            
            // Check each dependency
            foreach ($dependencies as $dependency) {
                // If a dependency is being disabled but the module is enabled
                if (isset($input[$dependency]) && !$input[$dependency]) {
                    // Force enable the dependency
                    $validated[$dependency] = true;
                    
                    // Add admin notice about the forced dependency
                    add_action('admin_notices', function() use ($module, $dependency) {
                        echo '<div class="notice notice-warning is-dismissible">';
                        printf(
                            __('<p>The <strong>%s</strong> module requires the <strong>%s</strong> module. The dependency has been automatically enabled.</p>', 'fluxstack'),
                            ucfirst(str_replace('-', ' ', $module)),
                            ucfirst(str_replace('-', ' ', $dependency))
                        );
                        echo '</div>';
                    });
                }
            }
        }
        
        return $validated;
    }
    
    /**
     * Sanitize block settings
     */
    public static function sanitize_block_settings($input) {
        // Check if the form was submitted (to handle case when all checkboxes are unchecked)
        $was_submitted = isset($_POST[self::BLOCK_OPTION_NAME . '_submitted']);
        
        // If form was submitted but input is empty, it means all checkboxes were unchecked
        if ($was_submitted && empty($input)) {
            $input = array();
        }
        
        $sanitized = array();
        
        // Process each block
        foreach (self::$default_blocks as $block => $default) {
            // If form was submitted, use the input value or false if not set
            // Otherwise, use the default value
            $sanitized[$block] = $was_submitted 
                ? (isset($input[$block]) ? (bool) $input[$block] : false)
                : $default;
        }
        
        // Validate block dependencies
        $sanitized = self::validate_block_dependencies($sanitized);
        
        return $sanitized;
    }
    
    /**
     * Validate block dependencies before saving settings
     */
    public static function validate_block_dependencies($input) {
        $validated = $input;
        $module_settings = self::get_module_settings();
        
        // Check block-to-block dependencies
        foreach (self::$block_dependencies as $block => $dependencies) {
            // Skip if block is disabled or not in input
            if (!isset($input[$block]) || !$input[$block]) {
                continue;
            }
            
            // Check each dependency
            foreach ($dependencies as $dependency) {
                // If a dependency is being disabled but the block is enabled
                if (isset($input[$dependency]) && !$input[$dependency]) {
                    // Force enable the dependency
                    $validated[$dependency] = true;
                    
                    // Add admin notice about the forced dependency
                    add_action('admin_notices', function() use ($block, $dependency) {
                        echo '<div class="notice notice-warning is-dismissible">';
                        printf(
                            __('<p>The <strong>%s</strong> block requires the <strong>%s</strong> block. The dependency has been automatically enabled.</p>', 'fluxstack'),
                            ucfirst(str_replace('-', ' ', $block)),
                            ucfirst(str_replace('-', ' ', $dependency))
                        );
                        echo '</div>';
                    });
                }
            }
        }
        
        // Check block-to-module dependencies
        foreach (self::$block_module_dependencies as $block => $module_deps) {
            // Skip if block is disabled or not in input
            if (!isset($input[$block]) || !$input[$block]) {
                continue;
            }
            
            // Check each module dependency
            foreach ($module_deps as $module) {
                // If a required module is disabled
                if (!isset($module_settings[$module]) || !$module_settings[$module]) {
                    // Disable the block that depends on it
                    $validated[$block] = false;
                    
                    // Add admin notice
                    add_action('admin_notices', function() use ($block, $module) {
                        echo '<div class="notice notice-warning is-dismissible">';
                        printf(
                            __('<p>The <strong>%s</strong> block requires the <strong>%s</strong> module which is disabled. The block has been automatically disabled.</p>', 'fluxstack'),
                            ucfirst(str_replace('-', ' ', $block)),
                            ucfirst(str_replace('-', ' ', $module))
                        );
                        echo '</div>';
                    });
                    
                    break;
                }
            }
        }
        
        return $validated;
    }
    
    /**
     * Handle reset to defaults
     */
    public static function handle_reset_defaults() {
        if (isset($_GET['page']) && $_GET['page'] === 'fluxstack-settings' && 
            isset($_GET['reset-defaults']) && $_GET['reset-defaults'] === '1') {
            
            // Verify nonce
            if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'reset-defaults')) {
                // Reset modules to defaults
                update_option(self::OPTION_NAME, self::$default_modules);
                
                // Reset blocks to defaults
                update_option(self::BLOCK_OPTION_NAME, self::$default_blocks);
                
                // Redirect to settings page
                wp_redirect(admin_url('themes.php?page=fluxstack-settings&reset=true'));
                exit;
            }
        }
    }
    
    /**
     * Render settings page
     */
    public static function render_settings_page() {
        $module_settings = self::get_module_settings();
        $block_settings = self::get_block_settings();
        
        // Discover blocks from the filesystem
        $discovered_blocks = self::discover_blocks();
        
        // Merge discovered blocks with default blocks
        self::$default_blocks = array_merge(self::$default_blocks, $discovered_blocks);
        ?>
        <div class="wrap fluxstack-settings-wrap">
            <h1><?php _e('FluxStack Theme Settings', 'fluxstack'); ?></h1>
            
            <?php if (isset($_GET['reset']) && $_GET['reset'] === 'true') : ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php _e('Settings have been reset to defaults.', 'fluxstack'); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') : ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php _e('Settings saved successfully.', 'fluxstack'); ?></p>
                </div>
            <?php endif; ?>
            
            <form method="post" action="options.php">
                <?php settings_fields('fluxstack_settings'); ?>
                <?php // These hidden fields ensure the options are submitted even if all checkboxes are unchecked ?>
                <input type="hidden" name="<?php echo esc_attr(self::OPTION_NAME); ?>_submitted" value="1">
                <input type="hidden" name="<?php echo esc_attr(self::BLOCK_OPTION_NAME); ?>_submitted" value="1">
                
                <div class="fluxstack-settings-container">
                    <?php foreach (self::$module_groups as $group_id => $group) : ?>
                        <div class="fluxstack-settings-group">
                            <h2><?php echo esc_html($group['title']); ?></h2>
                            
                            <table class="form-table">
                                <?php foreach ($group['modules'] as $module) : ?>
                                    <?php self::render_module_row($module, $module_settings); ?>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="fluxstack-settings-group">
                        <h2><?php _e('Theme Blocks', 'fluxstack'); ?></h2>
                        
                        <table class="form-table">
                            <?php foreach (self::$default_blocks as $block => $default) : ?>
                                <?php self::render_block_row($block, $block_settings, $module_settings); ?>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
                
                <div class="fluxstack-settings-actions">
                    <?php submit_button(); ?>
                    <a href="<?php echo esc_url(wp_nonce_url(add_query_arg('reset-defaults', '1'), 'reset-defaults')); ?>" class="button button-secondary">
                        <?php _e('Reset to Defaults', 'fluxstack'); ?>
                    </a>
                </div>
            </form>
        </div>
        <?php
    }
    
    /**
     * Render block settings row
     */
    private static function render_block_row($block, $settings, $module_settings) {
        $is_enabled = isset($settings[$block]) ? $settings[$block] : true;
        $has_block_dependencies = !empty(self::$block_dependencies[$block]);
        $has_module_dependencies = !empty(self::$block_module_dependencies[$block]);
        $is_dependency_for_others = false;
        
        // Check if this block is a dependency for other blocks
        foreach (self::$block_dependencies as $dependent => $dependencies) {
            if (in_array($block, $dependencies)) {
                $is_dependency_for_others = true;
                break;
            }
        }
        
        // Get dependency data attributes
        $dependency_attrs = '';
        if ($has_block_dependencies) {
            $dependency_attrs .= ' data-depends-on-blocks="' . implode(',', self::$block_dependencies[$block]) . '"';
        }
        
        // Determine if block should be disabled in UI
        $is_disabled = false;
        
        // Check block dependencies
        if ($has_block_dependencies) {
            foreach (self::$block_dependencies[$block] as $dependency) {
                if (isset($settings[$dependency]) && !$settings[$dependency]) {
                    $is_disabled = true;
                    break;
                }
            }
        }
        
        // Check module dependencies
        if (!$is_disabled && $has_module_dependencies) {
            foreach (self::$block_module_dependencies[$block] as $module) {
                if (!isset($module_settings[$module]) || !$module_settings[$module]) {
                    $is_disabled = true;
                    break;
                }
            }
        }
        
        // Render the block row with appropriate classes and attributes
        ?>
        <tr class="module-row <?php echo $is_disabled ? 'disabled-by-dependency' : ''; ?> <?php echo $is_dependency_for_others ? 'is-dependency' : ''; ?>">
            <td colspan="2">
                <div class="module-header">
                    <div class="module-title">
                        <label for="fluxstack-block-<?php echo esc_attr($block); ?>">
                            <?php echo esc_html(ucfirst(str_replace('-', ' ', $block))); ?>
                            <?php if ($is_dependency_for_others): ?>
                                <span class="dependency-badge" title="<?php esc_attr_e('Other blocks depend on this', 'fluxstack'); ?>">*</span>
                            <?php endif; ?>
                        </label>
                    </div>
                    <div class="module-toggle">
                        <label class="fluxstack-toggle">
                            <input type="checkbox" 
                                id="fluxstack-block-<?php echo esc_attr($block); ?>" 
                                name="<?php echo esc_attr(self::BLOCK_OPTION_NAME); ?>[<?php echo esc_attr($block); ?>]" 
                                value="1" 
                                class="block-checkbox"
                                data-block="<?php echo esc_attr($block); ?>"
                                <?php echo $dependency_attrs; ?>
                                <?php checked($is_enabled); ?>
                                <?php disabled($is_disabled); ?>>
                            <span class="fluxstack-toggle-slider"></span>
                        </label>
                    </div>
                </div>
                <div class="module-details">
                    <div class="module-description">
                        <?php echo isset(self::$block_descriptions[$block]) ? esc_html(self::$block_descriptions[$block]) : ''; ?>
                    </div>
                    <div class="dependency-info">
                        <?php echo self::get_block_dependency_html($block); ?>
                    </div>
                    <?php if ($is_dependency_for_others): ?>
                        <div class="dependency-warning" data-for="<?php echo esc_attr($block); ?>" style="display: none;">
                            <p class="warning-text">
                                <?php _e('Warning: Disabling this block will also disable blocks that depend on it.', 'fluxstack'); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php
    }
    
    /**
     * Render module settings row
     */
    private static function render_module_row($module, $settings) {
        $is_enabled = isset($settings[$module]) ? $settings[$module] : true;
        $has_dependencies = !empty(self::$module_dependencies[$module]);
        $is_dependency_for_others = false;
        
        // Check if this module is a dependency for any other module
        foreach (self::$module_dependencies as $dependent => $dependencies) {
            if (in_array($module, $dependencies)) {
                $is_dependency_for_others = true;
                break;
            }
        }
        
        // Get dependency data attributes
        $dependency_attrs = '';
        if ($has_dependencies) {
            $dependency_attrs .= ' data-depends-on="' . implode(',', self::$module_dependencies[$module]) . '"';
        }
        
        // Determine if module should be disabled in UI
        $is_disabled = false;
        if ($has_dependencies) {
            foreach (self::$module_dependencies[$module] as $dependency) {
                if (isset($settings[$dependency]) && !$settings[$dependency]) {
                    $is_disabled = true;
                    break;
                }
            }
        }
        
        // Render the module row with appropriate classes and attributes
        ?>
        <tr class="module-row <?php echo $is_disabled ? 'disabled-by-dependency' : ''; ?> <?php echo $is_dependency_for_others ? 'is-dependency' : ''; ?>">
            <td colspan="2">
                <div class="module-header">
                    <div class="module-title">
                        <label for="fluxstack-module-<?php echo esc_attr($module); ?>">
                            <?php echo esc_html(ucfirst(str_replace('-', ' ', $module))); ?>
                            <?php if ($is_dependency_for_others): ?>
                                <span class="dependency-badge" title="<?php esc_attr_e('Other modules depend on this', 'fluxstack'); ?>">*</span>
                            <?php endif; ?>
                        </label>
                    </div>
                    <div class="module-toggle">
                        <label class="fluxstack-toggle">
                            <input type="checkbox" 
                                id="fluxstack-module-<?php echo esc_attr($module); ?>" 
                                name="<?php echo esc_attr(self::OPTION_NAME); ?>[<?php echo esc_attr($module); ?>]" 
                                value="1" 
                                class="module-checkbox"
                                data-module="<?php echo esc_attr($module); ?>"
                                <?php echo $dependency_attrs; ?>
                                <?php checked($is_enabled); ?>
                                <?php disabled($is_disabled); ?>>
                            <span class="fluxstack-toggle-slider"></span>
                        </label>
                    </div>
                </div>
                <div class="module-details">
                    <div class="module-description">
                        <?php echo esc_html(self::$module_descriptions[$module]); ?>
                    </div>
                    <div class="dependency-info">
                        <?php echo self::get_dependency_html($module); ?>
                    </div>
                    <?php if ($is_dependency_for_others): ?>
                        <div class="dependency-warning" data-for="<?php echo esc_attr($module); ?>" style="display: none;">
                            <p class="warning-text">
                                <?php _e('Warning: Disabling this module will also disable modules that depend on it.', 'fluxstack'); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php
    }
    
    /**
     * Get module dependencies HTML
     */
    private static function get_dependency_html($module) {
        $html = '';
        $dependencies = array();
        
        // Find all modules that depend on this one
        foreach (self::$module_dependencies as $dependent => $required_modules) {
            if (in_array($module, $required_modules)) {
                $dependencies[] = ucfirst(str_replace('-', ' ', $dependent));
            }
        }
        
        if (!empty($dependencies)) {
            $html .= '<div class="module-dependency-notice">';
            $html .= '<strong>' . __('Required by:', 'fluxstack') . '</strong> ';
            $html .= implode(', ', $dependencies);
            $html .= '</div>';
        }
        
        // Show what this module depends on
        if (isset(self::$module_dependencies[$module]) && !empty(self::$module_dependencies[$module])) {
            $html .= '<div class="module-requires-notice">';
            $html .= '<strong>' . __('Requires:', 'fluxstack') . '</strong> ';
            
            $required = array();
            foreach (self::$module_dependencies[$module] as $dependency) {
                $required[] = ucfirst(str_replace('-', ' ', $dependency));
            }
            
            $html .= implode(', ', $required);
            $html .= '</div>';
        }
        
        return $html;
    }
    
    /**
     * Enqueue admin assets
     */
    public static function enqueue_admin_assets($hook) {
        if ($hook !== 'appearance_page_fluxstack-settings') {
            return;
        }
        
        // Enqueue CSS and JS for the settings page
        wp_enqueue_style('fluxstack-admin', get_stylesheet_directory_uri() . '/modules/module-manager/assets/css/admin.css', array(), '1.0.0');
        wp_enqueue_script('fluxstack-admin', get_stylesheet_directory_uri() . '/modules/module-manager/assets/js/admin.js', array('jquery'), '1.0.0', true);
    }
    
    /**
     * Add settings link
     */
    public static function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('themes.php?page=fluxstack-settings') . '">' . __('Settings', 'fluxstack') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
    
    /**
     * Get module settings
     */
    public static function get_module_settings() {
        $settings = get_option(self::OPTION_NAME, self::$default_modules);
        return wp_parse_args($settings, self::$default_modules);
    }
    
    /**
     * Check if a module is enabled
     */
    public static function is_module_enabled($module) {
        $settings = self::get_module_settings();
        return isset($settings[$module]) ? (bool) $settings[$module] : true;
    }
    
    /**
     * Check if a module can be loaded
     */
    public static function can_load_module($module) {
        // If module is not enabled in settings, it can't be loaded
        if (!self::is_module_enabled($module)) {
            return false;
        }
        
        // Check if all dependencies are enabled
        if (isset(self::$module_dependencies[$module])) {
            foreach (self::$module_dependencies[$module] as $dependency) {
                if (!self::is_module_enabled($dependency)) {
                    // Log the dependency issue
                    error_log(sprintf(
                        'Module %s cannot be loaded because its dependency %s is not enabled',
                        $module,
                        $dependency
                    ));
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Get modules in dependency order
     */
    public static function get_modules_in_dependency_order() {
        $modules = array_keys(self::$default_modules);
        $dependencies = self::$module_dependencies;
        $sorted = array();
        $visited = array();
        
        // Helper function for depth-first search
        $visit = function($module) use (&$visit, &$sorted, &$visited, $dependencies) {
            // If already visited in this path, we have a circular dependency
            if (isset($visited[$module]) && $visited[$module] === true) {
                return;
            }
            
            // If not yet visited
            if (!isset($visited[$module])) {
                $visited[$module] = true;
                
                // Visit dependencies first
                if (isset($dependencies[$module])) {
                    foreach ($dependencies[$module] as $dependency) {
                        $visit($dependency);
                    }
                }
                
                // Add to sorted list
                $visited[$module] = false;
                $sorted[] = $module;
            }
        };
        
        // Visit each module
        foreach ($modules as $module) {
            $visit($module);
        }
        
        return $sorted;
    }
    
    /**
     * Get block settings
     */
    public static function get_block_settings() {
        $settings = get_option(self::BLOCK_OPTION_NAME, self::$default_blocks);
        return wp_parse_args($settings, self::$default_blocks);
    }
    
    /**
     * Check if a block is enabled
     */
    public static function is_block_enabled($block) {
        $settings = self::get_block_settings();
        return isset($settings[$block]) ? (bool) $settings[$block] : true;
    }
    
    /**
     * Check if a block can be loaded
     */
    public static function can_load_block($block) {
        // If block is not enabled in settings, it can't be loaded
        if (!self::is_block_enabled($block)) {
            return false;
        }
        
        // Check module dependencies
        if (isset(self::$block_module_dependencies[$block])) {
            foreach (self::$block_module_dependencies[$block] as $module) {
                if (!self::is_module_enabled($module)) {
                    // Log the dependency issue
                    error_log(sprintf(
                        'Block %s cannot be loaded because its module dependency %s is not enabled',
                        $block,
                        $module
                    ));
                    return false;
                }
            }
        }
        
        // Check block dependencies
        if (isset(self::$block_dependencies[$block])) {
            foreach (self::$block_dependencies[$block] as $dependency) {
                if (!self::can_load_block($dependency)) {
                    // Log the dependency issue
                    error_log(sprintf(
                        'Block %s cannot be loaded because its block dependency %s is not enabled',
                        $block,
                        $dependency
                    ));
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Discover blocks from the filesystem
     */
    public static function discover_blocks() {
        $blocks = array();
        $blocks_dir = get_stylesheet_directory() . '/native-blocks';
        
        // Skip if directory doesn't exist
        if (!is_dir($blocks_dir)) {
            return $blocks;
        }
        
        // Get all block directories
        $block_folders = array_filter(glob($blocks_dir . '/*'), 'is_dir');
        
        foreach ($block_folders as $block_folder) {
            $block_name = basename($block_folder);
            
            // Skip special directories
            if (in_array($block_name, array('block-styles', '_template'))) {
                continue;
            }
            
            // Add block to the list
            $blocks[$block_name] = true;
            
            // Check for block.json to get metadata
            $json_file = $block_folder . '/block.json';
            if (file_exists($json_file)) {
                $metadata = json_decode(file_get_contents($json_file), true);
                
                // Get block description
                if (isset($metadata['description'])) {
                    self::$block_descriptions[$block_name] = $metadata['description'];
                }
                
                // Get block dependencies
                if (isset($metadata['fluxstack'])) {
                    // Module dependencies
                    if (isset($metadata['fluxstack']['moduleDependencies']) && is_array($metadata['fluxstack']['moduleDependencies'])) {
                        self::$block_module_dependencies[$block_name] = $metadata['fluxstack']['moduleDependencies'];
                    }
                    
                    // Block dependencies
                    if (isset($metadata['fluxstack']['blockDependencies']) && is_array($metadata['fluxstack']['blockDependencies'])) {
                        self::$block_dependencies[$block_name] = $metadata['fluxstack']['blockDependencies'];
                    }
                }
            }
        }
        
        // Add block styles as a special case
        if (is_dir($blocks_dir . '/block-styles')) {
            $blocks['button-styles'] = true;
        }
        
        return $blocks;
    }
    
    /**
     * Get block dependencies HTML
     */
    private static function get_block_dependency_html($block) {
        $html = '';
        $dependencies = array();
        
        // Find all blocks that depend on this one
        foreach (self::$block_dependencies as $dependent => $required_blocks) {
            if (in_array($block, $required_blocks)) {
                $dependencies[] = ucfirst(str_replace('-', ' ', $dependent));
            }
        }
        
        if (!empty($dependencies)) {
            $html .= '<div class="block-dependency-notice">';
            $html .= '<strong>' . __('Required by:', 'fluxstack') . '</strong> ';
            $html .= implode(', ', $dependencies);
            $html .= '</div>';
        }
        
        // Show what this block depends on (other blocks)
        if (isset(self::$block_dependencies[$block]) && !empty(self::$block_dependencies[$block])) {
            $html .= '<div class="block-requires-notice">';
            $html .= '<strong>' . __('Requires blocks:', 'fluxstack') . '</strong> ';
            
            $required = array();
            foreach (self::$block_dependencies[$block] as $dependency) {
                $required[] = ucfirst(str_replace('-', ' ', $dependency));
            }
            
            $html .= implode(', ', $required);
            $html .= '</div>';
        }
        
        // Show what this block depends on (modules)
        if (isset(self::$block_module_dependencies[$block]) && !empty(self::$block_module_dependencies[$block])) {
            $html .= '<div class="block-requires-modules-notice">';
            $html .= '<strong>' . __('Requires modules:', 'fluxstack') . '</strong> ';
            
            $required = array();
            foreach (self::$block_module_dependencies[$block] as $dependency) {
                $required[] = ucfirst(str_replace('-', ' ', $dependency));
            }
            
            $html .= implode(', ', $required);
            $html .= '</div>';
        }
        
        return $html;
    }
}

// Initialize the module manager
FS_Module_Manager::init();
