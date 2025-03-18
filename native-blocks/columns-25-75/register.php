<?php
/**
 * Register Columns 25/75 Block
 *
 * @package FluxStack
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include block template
require_once dirname( __FILE__ ) . '/block.php';

/**
 * Register the block
 */
function fluxstack_register_columns_25_75_block() {
    // Check if block editor is available.
    if ( ! function_exists( 'register_block_type' ) ) {
        return;
    }

    // Get block name from directory name
    $block_name = basename( dirname( __FILE__ ) );
    
    // Register assets using the helper function
    fluxstack_register_block_assets( $block_name, array(
        'script_deps' => array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-block-editor' ),
        'editor_deps' => array( 'wp-edit-blocks' ),
        'style_deps'  => array(),
    ) );

    // Register the block
    register_block_type(
        get_stylesheet_directory() . '/native-blocks/' . $block_name . '/block.json',
        array(
            'render_callback' => 'fluxstack_render_columns_25_75_block_template',
        )
    );
}
add_action( 'init', 'fluxstack_register_columns_25_75_block', 10 );
