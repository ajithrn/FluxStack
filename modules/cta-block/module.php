<?php
/**
 * CTA Block — PHP-only (autoRegister)
 * No JavaScript, no build step. WordPress auto-generates editor controls.
 * Requires: WordPress 7.0+
 */

use App\Modules\BaseModule;

return new class extends BaseModule
{
    public function id(): string { return 'cta-block'; }
    public function name(): string { return 'CTA Banner'; }
    public function description(): string { return 'Call-to-action banner with heading, text, and button.'; }
    public function category(): string { return 'block'; }
    public function enabledByDefault(): bool { return true; }

    public function register(): void
    {
        add_action('init', [$this, 'registerBlock']);
        add_action('enqueue_block_editor_assets', [$this, 'editorStyles']);
    }

    public function registerBlock(): void
    {
        register_block_type('fluxstack/cta', [
            'title'       => __('CTA Banner', 'fluxstack'),
            'description' => __('Call-to-action banner with heading, text, and button.', 'fluxstack'),
            'category'    => 'fluxstack',
            'icon'        => 'megaphone',

            'attributes' => [
                'heading' => [
                    'type'    => 'string',
                    'default' => '',
                    'label'   => 'Heading',
                ],
                'text' => [
                    'type'    => 'string',
                    'default' => '',
                    'label'   => 'Description',
                ],
                'buttonText' => [
                    'type'    => 'string',
                    'default' => '',
                    'label'   => 'Button Text',
                ],
                'buttonUrl' => [
                    'type'    => 'string',
                    'default' => '',
                    'label'   => 'Button URL',
                ],
                'layout' => [
                    'type'    => 'string',
                    'enum'    => ['Horizontal', 'Stacked'],
                    'default' => 'Horizontal',
                    'label'   => 'Layout',
                ],
                'openInNewTab' => [
                    'type'    => 'boolean',
                    'default' => false,
                    'label'   => 'Open in New Tab',
                ],
            ],

            'supports' => [
                'autoRegister' => true,
                'align'        => ['wide', 'full'],
                'color'        => ['text' => true, 'background' => true],
                'spacing'      => ['padding' => true],
                'border'       => ['radius' => true],
            ],

            'render_callback' => [$this, 'render'],
        ]);

        wp_enqueue_block_style('fluxstack/cta', [
            'handle' => 'fluxstack-cta',
            'src'    => get_theme_file_uri('modules/cta-block/style.css'),
            'path'   => get_theme_file_path('modules/cta-block/style.css'),
        ]);
    }

    public function render(array $attributes): string
    {
        $layout = strtolower($attributes['layout'] ?? 'horizontal');
        $wrapper = get_block_wrapper_attributes([
            'class' => 'fluxstack-cta fluxstack-cta--' . esc_attr($layout),
        ]);

        $target = ! empty($attributes['openInNewTab']) ? ' target="_blank" rel="noopener noreferrer"' : '';

        $button = '';
        if (! empty($attributes['buttonText']) && ! empty($attributes['buttonUrl'])) {
            $button = sprintf(
                '<div class="fluxstack-cta__action"><a class="fluxstack-cta__button" href="%s"%s>%s</a></div>',
                esc_url($attributes['buttonUrl']),
                $target,
                esc_html($attributes['buttonText'])
            );
        }

        return sprintf(
            '<section %s><div class="fluxstack-cta__content"><h2 class="fluxstack-cta__heading">%s</h2><p class="fluxstack-cta__text">%s</p></div>%s</section>',
            $wrapper,
            esc_html($attributes['heading']),
            esc_html($attributes['text']),
            $button
        );
    }

    public function editorStyles(): void
    {
        wp_enqueue_style('fluxstack-cta', get_theme_file_uri('modules/cta-block/style.css'));
    }
};
