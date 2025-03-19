<?php
/**
 * Native Blocks Loader
 *
 * @package FluxStack
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Helper function to register block assets
 *
 * @param string $block_name Block name without prefix (e.g. 'columns-25-75').
 * @param array  $args       Optional arguments.
 */
function fluxstack_register_block_assets( $block_name, $args = array() ) {
    $defaults = array(
        'has_script'    => true,
        'has_style'     => true,
        'has_editor'    => true,
        'script_deps'   => array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-block-editor' ),
        'style_deps'    => array(),
        'editor_deps'   => array( 'wp-edit-blocks' ),
    );

    $args = wp_parse_args( $args, $defaults );
    $block_path = get_stylesheet_directory() . '/native-blocks/' . $block_name;
    $block_url = get_stylesheet_directory_uri() . '/native-blocks/' . $block_name;

    // Register script if it exists
    if ( $args['has_script'] && file_exists( $block_path . '/build.js' ) ) {
        wp_register_script(
            'fluxstack-' . $block_name,
            $block_url . '/build.js',
            $args['script_deps'],
            filemtime( $block_path . '/build.js' ),
            true
        );
    }

    // Register editor style if it exists
    if ( $args['has_editor'] && file_exists( $block_path . '/editor.css' ) ) {
        wp_register_style(
            'fluxstack-' . $block_name . '-editor',
            $block_url . '/editor.css',
            $args['editor_deps'],
            filemtime( $block_path . '/editor.css' )
        );
    }

    // Register frontend style if it exists
    if ( $args['has_style'] && file_exists( $block_path . '/style.css' ) ) {
        wp_register_style(
            'fluxstack-' . $block_name . '-style',
            $block_url . '/style.css',
            $args['style_deps'],
            filemtime( $block_path . '/style.css' )
        );
    }
}

/**
 * Auto-discover and load all blocks
 */
function fluxstack_load_blocks() {
    // Make sure the register_block_type function exists
    if ( ! function_exists( 'register_block_type' ) ) {
        return;
    }
    
    // Get all block directories
    $blocks_dir = get_stylesheet_directory() . '/native-blocks';
    $block_folders = array_filter( glob( $blocks_dir . '/*' ), 'is_dir' );
    
    // Load each block's register.php file
    foreach ( $block_folders as $block_folder ) {
        // Skip the block-styles directory as it's handled separately
        if ( basename( $block_folder ) === 'block-styles' ) {
            continue;
        }
        
        $register_file = $block_folder . '/register.php';
        if ( file_exists( $register_file ) ) {
            require_once $register_file;
        }
    }
}
add_action( 'init', 'fluxstack_load_blocks', 9 );

/**
 * Register and enqueue block styles
 */
function fluxstack_register_block_styles() {
    // Register block styles script
    wp_register_script(
        'fluxstack-block-styles',
        get_stylesheet_directory_uri() . '/native-blocks/block-styles/button-styles.js',
        array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post', 'wp-i18n' ),
        filemtime( get_stylesheet_directory() . '/native-blocks/block-styles/button-styles.js' ),
        true
    );
    
    // Register block styles CSS
    wp_register_style(
        'fluxstack-block-styles',
        get_stylesheet_directory_uri() . '/native-blocks/block-styles/button-styles.css',
        array(),
        filemtime( get_stylesheet_directory() . '/native-blocks/block-styles/button-styles.css' )
    );
    
    // Enqueue for editor
    if ( is_admin() ) {
        wp_enqueue_script( 'fluxstack-block-styles' );
    }
    
    // Enqueue for frontend and editor
    wp_enqueue_style( 'fluxstack-block-styles' );
}
add_action( 'init', 'fluxstack_register_block_styles', 10 );

/**
 * Initialize ACF Blocks
 */
function fluxstack_init_acf_blocks() {
    // Check if ACF Pro is active
    if ( ! function_exists( 'acf_register_block_type' ) ) {
        return;
    }

    // ACF blocks will be added here
    // You can use a similar auto-discovery approach for ACF blocks if needed
}
add_action( 'acf/init', 'fluxstack_init_acf_blocks' );

/**
 * Add custom block category
 *
 * @param array $categories Block categories.
 * @return array Modified block categories.
 */
function fluxstack_block_category( $categories ) {
    return array_merge(
        array(
            array(
                'slug'  => 'fluxstack',
                'title' => __( 'Theme Blocks', 'fluxstack' ),
            ),
        ),
        $categories
    );
}
add_filter( 'block_categories_all', 'fluxstack_block_category', 10, 1 );
