<?php
/**
 * Hero Block render template.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block inner content.
 * @var WP_Block $block      Block instance.
 */

$heading = $attributes['heading'] ?? '';
$subheading = $attributes['subheading'] ?? '';
$bg_image = $attributes['backgroundImage'] ?? [];
$overlay_opacity = $attributes['overlayOpacity'] ?? 50;
$cta_text = $attributes['ctaText'] ?? '';
$cta_url = $attributes['ctaUrl'] ?? '';
$cta_secondary_text = $attributes['ctaSecondaryText'] ?? '';
$cta_secondary_url = $attributes['ctaSecondaryUrl'] ?? '';
$min_height = $attributes['minHeight'] ?? '60vh';
$text_align = $attributes['textAlign'] ?? 'center';

$bg_style = '';
if (! empty($bg_image['url'])) {
    $bg_style = "background-image: url('" . esc_url($bg_image['url']) . "');";
}

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'fluxstack-hero fluxstack-hero--align-' . esc_attr($text_align),
    'style' => $bg_style . ' min-height: ' . esc_attr($min_height) . ';',
]);
?>
<section <?php echo $wrapper_attributes; ?>>
    <div class="fluxstack-hero__overlay" style="opacity: <?php echo esc_attr($overlay_opacity / 100); ?>;"></div>
    <div class="fluxstack-hero__content">
        <?php if ($heading) : ?>
            <h1 class="fluxstack-hero__heading"><?php echo wp_kses_post($heading); ?></h1>
        <?php endif; ?>
        <?php if ($subheading) : ?>
            <p class="fluxstack-hero__subheading"><?php echo wp_kses_post($subheading); ?></p>
        <?php endif; ?>
        <?php if ($cta_text || $cta_secondary_text) : ?>
            <div class="fluxstack-hero__actions">
                <?php if ($cta_text && $cta_url) : ?>
                    <a href="<?php echo esc_url($cta_url); ?>" class="fluxstack-hero__cta fluxstack-hero__cta--primary"><?php echo esc_html($cta_text); ?></a>
                <?php endif; ?>
                <?php if ($cta_secondary_text && $cta_secondary_url) : ?>
                    <a href="<?php echo esc_url($cta_secondary_url); ?>" class="fluxstack-hero__cta fluxstack-hero__cta--secondary"><?php echo esc_html($cta_secondary_text); ?></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
