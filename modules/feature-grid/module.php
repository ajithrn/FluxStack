<?php
/**
 * Feature Grid Block — PHP-only (autoRegister)
 * Grid of feature cards. Content managed via Site Settings or render callback.
 */
use App\Modules\BaseModule;

return new class extends BaseModule {
    public function id(): string { return 'feature-grid'; }
    public function name(): string { return 'Feature Grid'; }
    public function description(): string { return 'Grid of feature cards with icons, titles, and descriptions.'; }
    public function category(): string { return 'block'; }

    public function register(): void {
        add_action('init', [$this, 'registerBlock']);
        add_action('enqueue_block_editor_assets', [$this, 'editorStyles']);
    }

    public function registerBlock(): void {
        register_block_type('fluxstack/feature-grid', [
            'title'    => __('Feature Grid', 'fluxstack'),
            'category' => 'fluxstack',
            'icon'     => 'grid-view',
            'attributes' => [
                'columns'  => ['type' => 'string', 'enum' => ['2', '3', '4'], 'default' => '3', 'label' => 'Columns'],
                'feature1Title' => ['type' => 'string', 'default' => '', 'label' => 'Feature 1 Title'],
                'feature1Desc'  => ['type' => 'string', 'default' => '', 'label' => 'Feature 1 Description'],
                'feature1Icon'  => ['type' => 'string', 'default' => '', 'label' => 'Feature 1 Icon (dashicon name)'],
                'feature2Title' => ['type' => 'string', 'default' => '', 'label' => 'Feature 2 Title'],
                'feature2Desc'  => ['type' => 'string', 'default' => '', 'label' => 'Feature 2 Description'],
                'feature2Icon'  => ['type' => 'string', 'default' => '', 'label' => 'Feature 2 Icon'],
                'feature3Title' => ['type' => 'string', 'default' => '', 'label' => 'Feature 3 Title'],
                'feature3Desc'  => ['type' => 'string', 'default' => '', 'label' => 'Feature 3 Description'],
                'feature3Icon'  => ['type' => 'string', 'default' => '', 'label' => 'Feature 3 Icon'],
            ],
            'supports' => [
                'autoRegister' => true,
                'align' => ['wide', 'full'],
                'color' => ['text' => true, 'background' => true],
                'spacing' => ['padding' => true],
            ],
            'render_callback' => [$this, 'render'],
        ]);

        wp_enqueue_block_style('fluxstack/feature-grid', [
            'handle' => 'fluxstack-feature-grid',
            'src'    => get_theme_file_uri('modules/feature-grid/style.css'),
            'path'   => get_theme_file_path('modules/feature-grid/style.css'),
        ]);
    }

    public function render(array $attributes): string {
        $cols = $attributes['columns'] ?? '3';
        $wrapper = get_block_wrapper_attributes([
            'class' => 'fluxstack-feature-grid fluxstack-feature-grid--cols-' . esc_attr($cols),
        ]);

        $cards = '';
        for ($i = 1; $i <= 3; $i++) {
            $title = $attributes["feature{$i}Title"] ?? '';
            $desc = $attributes["feature{$i}Desc"] ?? '';
            $icon = $attributes["feature{$i}Icon"] ?? '';
            if (empty($title)) continue;

            $iconHtml = $icon ? '<span class="dashicons dashicons-' . esc_attr($icon) . ' fluxstack-feature-grid__icon"></span>' : '';
            $cards .= sprintf(
                '<div class="fluxstack-feature-grid__card">%s<h3 class="fluxstack-feature-grid__title">%s</h3><p class="fluxstack-feature-grid__desc">%s</p></div>',
                $iconHtml, esc_html($title), esc_html($desc)
            );
        }

        return sprintf('<div %s>%s</div>', $wrapper, $cards);
    }

    public function editorStyles(): void {
        wp_enqueue_style('fluxstack-feature-grid', get_theme_file_uri('modules/feature-grid/style.css'));
    }
};
