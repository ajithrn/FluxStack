<?php
/**
 * Stats Counter Block — PHP-only (autoRegister)
 * Animated number counters with labels.
 */
use App\Modules\BaseModule;

return new class extends BaseModule {
    public function id(): string { return 'stats-counter'; }
    public function name(): string { return 'Stats Counter'; }
    public function description(): string { return 'Animated number counters with labels for showcasing metrics.'; }
    public function category(): string { return 'block'; }

    public function register(): void {
        add_action('init', [$this, 'registerBlock']);
        add_action('init', [$this, 'registerScript']);
        add_action('enqueue_block_editor_assets', [$this, 'editorStyles']);
    }

    public function registerScript(): void {
        wp_register_script('fluxstack-stats-counter', get_theme_file_uri('modules/stats-counter/frontend.js'), [], '1.0', true);
    }

    public function registerBlock(): void {
        register_block_type('fluxstack/stats-counter', [
            'title'    => __('Stats Counter', 'fluxstack'),
            'category' => 'fluxstack',
            'icon'     => 'chart-bar',
            'attributes' => [
                'stat1Value'  => ['type' => 'string', 'default' => '', 'label' => 'Stat 1 Number'],
                'stat1Label'  => ['type' => 'string', 'default' => '', 'label' => 'Stat 1 Label'],
                'stat1Suffix' => ['type' => 'string', 'default' => '', 'label' => 'Stat 1 Suffix (e.g. +, %, K)'],
                'stat2Value'  => ['type' => 'string', 'default' => '', 'label' => 'Stat 2 Number'],
                'stat2Label'  => ['type' => 'string', 'default' => '', 'label' => 'Stat 2 Label'],
                'stat2Suffix' => ['type' => 'string', 'default' => '', 'label' => 'Stat 2 Suffix'],
                'stat3Value'  => ['type' => 'string', 'default' => '', 'label' => 'Stat 3 Number'],
                'stat3Label'  => ['type' => 'string', 'default' => '', 'label' => 'Stat 3 Label'],
                'stat3Suffix' => ['type' => 'string', 'default' => '', 'label' => 'Stat 3 Suffix'],
                'stat4Value'  => ['type' => 'string', 'default' => '', 'label' => 'Stat 4 Number'],
                'stat4Label'  => ['type' => 'string', 'default' => '', 'label' => 'Stat 4 Label'],
                'stat4Suffix' => ['type' => 'string', 'default' => '', 'label' => 'Stat 4 Suffix'],
                'columns'     => ['type' => 'string', 'enum' => ['2', '3', '4'], 'default' => '4', 'label' => 'Columns'],
            ],
            'supports' => [
                'autoRegister' => true,
                'align' => ['wide', 'full'],
                'color' => ['text' => true, 'background' => true],
                'spacing' => ['padding' => true],
            ],
            'render_callback' => [$this, 'render'],
        ]);

        wp_enqueue_block_style('fluxstack/stats-counter', [
            'handle' => 'fluxstack-stats-counter',
            'src'    => get_theme_file_uri('modules/stats-counter/style.css'),
            'path'   => get_theme_file_path('modules/stats-counter/style.css'),
        ]);
    }

    public function render(array $attributes): string {
        wp_enqueue_script('fluxstack-stats-counter');

        $cols = $attributes['columns'] ?? '4';
        $wrapper = get_block_wrapper_attributes([
            'class' => 'fluxstack-stats fluxstack-stats--cols-' . esc_attr($cols),
        ]);

        $items = '';
        for ($i = 1; $i <= 4; $i++) {
            $value = $attributes["stat{$i}Value"] ?? '';
            $label = $attributes["stat{$i}Label"] ?? '';
            $suffix = $attributes["stat{$i}Suffix"] ?? '';
            if (empty($value)) continue;

            $items .= sprintf(
                '<div class="fluxstack-stats__item"><span class="fluxstack-stats__value" data-target="%s">0</span><span class="fluxstack-stats__suffix">%s</span><span class="fluxstack-stats__label">%s</span></div>',
                esc_attr($value), esc_html($suffix), esc_html($label)
            );
        }

        return sprintf('<div %s>%s</div>', $wrapper, $items);
    }

    public function editorStyles(): void {
        wp_enqueue_style('fluxstack-stats-counter', get_theme_file_uri('modules/stats-counter/style.css'));
    }
};
