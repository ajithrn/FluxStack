<?php
/**
 * Native Blocks Loader
 *
 * @package FluxStack
 */

/**
 * Initialize ACF Blocks
 */
function fluxstack_init_blocks() {
    // Check if ACF Pro is active
    if ( ! function_exists( 'acf_register_block_type' ) ) {
        return;
    }

    // Blocks will be added here
}
add_action( 'acf/init', 'fluxstack_init_blocks' );

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
                'title' => __( 'FluxStack', 'fluxstack' ),
            ),
        ),
        $categories
    );
}
add_filter( 'block_categories_all', 'fluxstack_block_category', 10, 1 );
