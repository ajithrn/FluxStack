<?php
/**
 * Columns 25/75 Block Template
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
function fluxstack_render_columns_25_75_block_template( $attributes, $content ) {
    // Extract attributes with defaults
    $className = isset( $attributes['className'] ) ? $attributes['className'] : '';
    $anchor = isset( $attributes['anchor'] ) ? $attributes['anchor'] : '';
    $align = isset( $attributes['align'] ) ? 'align' . $attributes['align'] : '';
    
    // Build the CSS classes
    $classes = array( 'fluxstack-columns-25-75' );
    if ( ! empty( $className ) ) {
        $classes[] = $className;
    }
    if ( ! empty( $align ) ) {
        $classes[] = $align;
    }
    
    // Start output buffering
    ob_start();
    ?>
    <div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo ! empty( $anchor ) ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?>>
        <?php echo $content; ?>
    </div>
    <?php
    // Return the buffered content
    return ob_get_clean();
}
