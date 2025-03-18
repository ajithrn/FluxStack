<?php
/**
 * Block Name Template
 *
 * @package FluxStack
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render the block
 *
 * @param array  $attributes The block attributes.
 * @param string $content    The block content.
 * @return string The block HTML.
 */
function fluxstack_render_block_name_block_template( $attributes, $content ) {
    // Extract attributes with defaults
    $className = isset( $attributes['className'] ) ? $attributes['className'] : '';
    
    // Start output buffering
    ob_start();
    ?>
    <div class="fluxstack-block-name <?php echo esc_attr( $className ); ?>">
        <?php echo $content; ?>
    </div>
    <?php
    // Return the buffered content
    return ob_get_clean();
}
