<?php
/**
 * Accordion/FAQ Block — JSX (repeater UI)
 * Dynamic FAQ items with add/remove/reorder in the editor sidebar.
 */
use App\Modules\BlockModule;

return new class extends BlockModule {
    public function id(): string { return 'accordion-block'; }
    public function name(): string { return 'Accordion / FAQ'; }
    public function description(): string { return 'Expandable accordion items for FAQs and collapsible content.'; }
    public function blockName(): string { return 'fluxstack/accordion'; }
    public function enabledByDefault(): bool { return true; }

    public function register(): void
    {
        add_action('init', [$this, 'registerBlock']);
        add_action('init', [$this, 'registerScript']);
        add_action('enqueue_block_editor_assets', [$this, 'editorStyles']);
    }

    public function registerBlock(): void
    {
        register_block_type($this->path(), [
            'render_callback' => [$this, 'render'],
        ]);
    }

    public function registerScript(): void
    {
        wp_register_script(
            'fluxstack-accordion',
            get_theme_file_uri('modules/accordion-block/frontend.js'),
            [],
            '1.0',
            true
        );
    }

    public function render(array $attributes): string
    {
        $items = $attributes['items'] ?? [];
        $openFirst = $attributes['openFirst'] ?? true;
        $variant = $attributes['variant'] ?? 'default';
        $iconStyle = $attributes['iconStyle'] ?? 'plus';
        $gap = $attributes['gap'] ?? 'compact';

        if (empty($items)) {
            return '';
        }

        wp_enqueue_script('fluxstack-accordion');

        $classes = sprintf(
            'fluxstack-accordion fluxstack-accordion--%s fluxstack-accordion--gap-%s fluxstack-accordion--icon-%s',
            esc_attr($variant),
            esc_attr($gap),
            esc_attr($iconStyle)
        );

        $wrapper = get_block_wrapper_attributes(['class' => $classes]);
        $html = '';

        foreach ($items as $index => $item) {
            $question = $item['question'] ?? '';
            $answer = $item['answer'] ?? '';
            if (empty($question)) continue;

            $open = ($index === 0 && $openFirst) ? ' open' : '';
            $html .= sprintf(
                '<details class="fluxstack-accordion__item"%s><summary class="fluxstack-accordion__question">%s</summary><div class="fluxstack-accordion__answer">%s</div></details>',
                $open,
                esc_html($question),
                wp_kses_post($answer)
            );
        }

        return sprintf('<div %s>%s</div>', $wrapper, $html);
    }

    public function editorStyles(): void
    {
        wp_enqueue_style(
            'fluxstack-accordion-editor',
            get_theme_file_uri('modules/accordion-block/style.css'),
            [],
            '1.0.0'
        );
    }
};
