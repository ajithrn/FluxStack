<?php
/**
 * CTA Block render template.
 */
$heading = $attributes['heading'] ?? '';
$text = $attributes['text'] ?? '';
$button_text = $attributes['buttonText'] ?? '';
$button_url = $attributes['buttonUrl'] ?? '';
$layout = $attributes['layout'] ?? 'horizontal';

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'fluxstack-cta fluxstack-cta--' . esc_attr($layout),
]);
?>
<section <?php echo $wrapper_attributes; ?>>
    <div class="fluxstack-cta__content">
        <?php if ($heading) : ?>
            <h2 class="fluxstack-cta__heading"><?php echo wp_kses_post($heading); ?></h2>
        <?php endif; ?>
        <?php if ($text) : ?>
            <p class="fluxstack-cta__text"><?php echo wp_kses_post($text); ?></p>
        <?php endif; ?>
    </div>
    <?php if ($button_text && $button_url) : ?>
        <div class="fluxstack-cta__action">
            <a href="<?php echo esc_url($button_url); ?>" class="fluxstack-cta__button"><?php echo esc_html($button_text); ?></a>
        </div>
    <?php endif; ?>
</section>
