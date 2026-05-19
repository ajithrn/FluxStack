<?php
/**
 * Hero Block — PHP-only (autoRegister)
 * Full-width hero section with background image, heading, subheading, and CTAs.
 * Requires: WordPress 7.0+
 */

use App\Modules\BaseModule;

return new class extends BaseModule
{
    public function id(): string { return 'hero-block'; }
    public function name(): string { return 'Hero Block'; }
    public function description(): string { return 'Full-width hero section with background, heading, and CTA buttons.'; }
    public function category(): string { return 'block'; }
    public function enabledByDefault(): bool { return true; }

    public function register(): void
    {
        add_action('init', [$this, 'registerBlock']);
        add_action('enqueue_block_editor_assets', [$this, 'editorStyles']);
    }

    public function registerBlock(): void
    {
        register_block_type('fluxstack/hero', [
            'title'       => __('Hero Section', 'fluxstack'),
            'description' => __('Full-width hero with background image, heading, and CTAs.', 'fluxstack'),
            'category'    => 'fluxstack',
            'icon'        => 'cover-image',

            'attributes' => [
                'heading' => [
                    'type'    => 'string',
                    'default' => '',
                    'label'   => 'Heading',
                ],
                'subheading' => [
                    'type'    => 'string',
                    'default' => '',
                    'label'   => 'Subheading',
                ],
                'backgroundUrl' => [
                    'type'    => 'string',
                    'default' => '',
                    'label'   => 'Background Image URL',
                ],
                'overlayOpacity' => [
                    'type'    => 'integer',
                    'default' => 50,
                    'label'   => 'Overlay Opacity (0-100)',
                ],
                'minHeight' => [
                    'type'    => 'string',
                    'default' => '60vh',
                    'label'   => 'Min Height (e.g. 60vh, 500px)',
                ],
                'textAlign' => [
                    'type'    => 'string',
                    'enum'    => ['Center', 'Left', 'Right'],
                    'default' => 'Center',
                    'label'   => 'Text Alignment',
                ],
                'ctaText' => [
                    'type'    => 'string',
                    'default' => '',
                    'label'   => 'Primary Button Text',
                ],
                'ctaUrl' => [
                    'type'    => 'string',
                    'default' => '',
                    'label'   => 'Primary Button URL',
                ],
                'ctaSecondaryText' => [
                    'type'    => 'string',
                    'default' => '',
                    'label'   => 'Secondary Button Text',
                ],
                'ctaSecondaryUrl' => [
                    'type'    => 'string',
                    'default' => '',
                    'label'   => 'Secondary Button URL',
                ],
            ],

            'supports' => [
                'autoRegister' => true,
                'align'        => ['wide', 'full'],
                'color'        => ['text' => true],
                'spacing'      => ['padding' => true],
            ],

            'render_callback' => [$this, 'render'],
        ]);

        wp_enqueue_block_style('fluxstack/hero', [
            'handle' => 'fluxstack-hero',
            'src'    => get_theme_file_uri('modules/hero-block/style.css'),
            'path'   => get_theme_file_path('modules/hero-block/style.css'),
        ]);
    }

    public function render(array $attributes): string
    {
        $align = strtolower($attributes['textAlign'] ?? 'center');
        $bgUrl = $attributes['backgroundUrl'] ?? '';
        $opacity = ($attributes['overlayOpacity'] ?? 50) / 100;
        $minHeight = $attributes['minHeight'] ?? '60vh';

        $style = 'min-height:' . esc_attr($minHeight) . ';';
        if ($bgUrl) {
            $style .= 'background-image:url(' . esc_url($bgUrl) . ');';
        }

        $wrapper = get_block_wrapper_attributes([
            'class' => 'fluxstack-hero fluxstack-hero--align-' . esc_attr($align),
            'style' => $style,
        ]);

        $buttons = '';
        if (! empty($attributes['ctaText']) && ! empty($attributes['ctaUrl'])) {
            $buttons .= sprintf(
                '<a class="fluxstack-hero__cta fluxstack-hero__cta--primary" href="%s">%s</a>',
                esc_url($attributes['ctaUrl']),
                esc_html($attributes['ctaText'])
            );
        }
        if (! empty($attributes['ctaSecondaryText']) && ! empty($attributes['ctaSecondaryUrl'])) {
            $buttons .= sprintf(
                '<a class="fluxstack-hero__cta fluxstack-hero__cta--secondary" href="%s">%s</a>',
                esc_url($attributes['ctaSecondaryUrl']),
                esc_html($attributes['ctaSecondaryText'])
            );
        }

        $actions = $buttons ? '<div class="fluxstack-hero__actions">' . $buttons . '</div>' : '';

        return sprintf(
            '<section %s><div class="fluxstack-hero__overlay" style="opacity:%s"></div><div class="fluxstack-hero__content">%s%s%s</div></section>',
            $wrapper,
            esc_attr($opacity),
            $attributes['heading'] ? '<h1 class="fluxstack-hero__heading">' . esc_html($attributes['heading']) . '</h1>' : '',
            $attributes['subheading'] ? '<p class="fluxstack-hero__subheading">' . esc_html($attributes['subheading']) . '</p>' : '',
            $actions
        );
    }

    public function editorStyles(): void
    {
        wp_enqueue_style('fluxstack-hero', get_theme_file_uri('modules/hero-block/style.css'));
    }
};
