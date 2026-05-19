<?php
/**
 * Icon Box Block — PHP-only (autoRegister)
 * Single icon + heading + text block.
 */
use App\Modules\BaseModule;

return new class extends BaseModule {
    public function id(): string { return 'icon-box'; }
    public function name(): string { return 'Icon Box'; }
    public function description(): string { return 'Single icon with heading and description text.'; }
    public function category(): string { return 'block'; }

    public function register(): void {
        add_action('init', [$this, 'registerBlock']);
        add_action('enqueue_block_editor_assets', [$this, 'editorStyles']);
    }

    public function registerBlock(): void {
        register_block_type('fluxstack/icon-box', [
            'title'    => __('Icon Box', 'fluxstack'),
            'category' => 'fluxstack',
            'icon'     => 'star-filled',
            'attributes' => [
                'icon'    => ['type' => 'string', 'default' => 'star-filled', 'label' => 'Dashicon Name'],
                'heading' => ['type' => 'string', 'default' => '', 'label' => 'Heading'],
                'text'    => ['type' => 'string', 'default' => '', 'label' => 'Description'],
                'layout'  => ['type' => 'string', 'enum' => ['Stacked', 'Horizontal'], 'default' => 'Stacked', 'label' => 'Layout'],
            ],
            'supports' => [
                'autoRegister' => true,
                'color' => ['text' => true, 'background' => true],
                'spacing' => ['padding' => true],
                'border' => ['radius' => true],
            ],
            'render_callback' => [$this, 'render'],
        ]);

        wp_enqueue_block_style('fluxstack/icon-box', [
            'handle' => 'fluxstack-icon-box',
            'src'    => get_theme_file_uri('modules/icon-box/style.css'),
            'path'   => get_theme_file_path('modules/icon-box/style.css'),
        ]);
    }

    public function render(array $attributes): string {
        $layout = strtolower($attributes['layout'] ?? 'stacked');
        $wrapper = get_block_wrapper_attributes([
            'class' => 'fluxstack-icon-box fluxstack-icon-box--' . esc_attr($layout),
        ]);

        $icon = $attributes['icon'] ? '<span class="dashicons dashicons-' . esc_attr($attributes['icon']) . ' fluxstack-icon-box__icon"></span>' : '';

        return sprintf(
            '<div %s>%s<div class="fluxstack-icon-box__content"><h3 class="fluxstack-icon-box__heading">%s</h3><p class="fluxstack-icon-box__text">%s</p></div></div>',
            $wrapper, $icon, esc_html($attributes['heading']), esc_html($attributes['text'])
        );
    }

    public function editorStyles(): void {
        wp_enqueue_style('fluxstack-icon-box', get_theme_file_uri('modules/icon-box/style.css'));
    }
};
