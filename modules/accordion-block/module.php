<?php
/**
 * Accordion/FAQ Block — PHP-only (autoRegister)
 * Expandable FAQ items with vanilla JS toggle.
 */
use App\Modules\BaseModule;

return new class extends BaseModule {
    public function id(): string { return 'accordion-block'; }
    public function name(): string { return 'Accordion / FAQ'; }
    public function description(): string { return 'Expandable accordion items for FAQs and collapsible content.'; }
    public function category(): string { return 'block'; }

    public function register(): void {
        add_action('init', [$this, 'registerBlock']);
        add_action('init', [$this, 'registerScript']);
        add_action('enqueue_block_editor_assets', [$this, 'editorStyles']);
    }

    public function registerScript(): void {
        wp_register_script('fluxstack-accordion', get_theme_file_uri('modules/accordion-block/frontend.js'), [], '1.0', true);
    }

    public function registerBlock(): void {
        register_block_type('fluxstack/accordion', [
            'title'    => __('Accordion', 'fluxstack'),
            'category' => 'fluxstack',
            'icon'     => 'list-view',
            'attributes' => [
                'item1Question' => ['type' => 'string', 'default' => '', 'label' => 'Item 1 Question'],
                'item1Answer'   => ['type' => 'string', 'default' => '', 'label' => 'Item 1 Answer'],
                'item2Question' => ['type' => 'string', 'default' => '', 'label' => 'Item 2 Question'],
                'item2Answer'   => ['type' => 'string', 'default' => '', 'label' => 'Item 2 Answer'],
                'item3Question' => ['type' => 'string', 'default' => '', 'label' => 'Item 3 Question'],
                'item3Answer'   => ['type' => 'string', 'default' => '', 'label' => 'Item 3 Answer'],
                'item4Question' => ['type' => 'string', 'default' => '', 'label' => 'Item 4 Question'],
                'item4Answer'   => ['type' => 'string', 'default' => '', 'label' => 'Item 4 Answer'],
                'item5Question' => ['type' => 'string', 'default' => '', 'label' => 'Item 5 Question'],
                'item5Answer'   => ['type' => 'string', 'default' => '', 'label' => 'Item 5 Answer'],
                'openFirst'     => ['type' => 'boolean', 'default' => true, 'label' => 'Open First Item'],
            ],
            'supports' => [
                'autoRegister' => true,
                'color' => ['text' => true, 'background' => true],
                'spacing' => ['padding' => true],
                'border' => ['radius' => true],
            ],
            'render_callback' => [$this, 'render'],
        ]);

        wp_enqueue_block_style('fluxstack/accordion', [
            'handle' => 'fluxstack-accordion',
            'src'    => get_theme_file_uri('modules/accordion-block/style.css'),
            'path'   => get_theme_file_path('modules/accordion-block/style.css'),
        ]);
    }

    public function render(array $attributes): string {
        // Show placeholder in editor when block is empty
        $hasItems = false;
        for ($i = 1; $i <= 5; $i++) {
            if (!empty($attributes["item{$i}Question"])) { $hasItems = true; break; }
        }

        if ($this->isEditorPreview() && !$hasItems) {
            $sample = '<div class="fluxstack-accordion" style="display:flex;flex-direction:column;gap:0.5rem;"><div style="border:1px solid rgba(0,0,0,0.08);border-radius:0.5rem;padding:1rem 1.25rem;"><strong>What is your return policy?</strong></div><div style="border:1px solid rgba(0,0,0,0.08);border-radius:0.5rem;padding:1rem 1.25rem;"><strong>How long does shipping take?</strong></div><div style="border:1px solid rgba(0,0,0,0.08);border-radius:0.5rem;padding:1rem 1.25rem;"><strong>Do you offer support?</strong></div></div>';
            return $this->renderPlaceholder('fluxstack-accordion', 'Accordion / FAQ', 'Add questions and answers to create expandable FAQ items.', $sample);
        }

        wp_enqueue_script('fluxstack-accordion');

        $wrapper = get_block_wrapper_attributes(['class' => 'fluxstack-accordion']);
        $items = '';

        for ($i = 1; $i <= 5; $i++) {
            $q = $attributes["item{$i}Question"] ?? '';
            $a = $attributes["item{$i}Answer"] ?? '';
            if (empty($q)) continue;

            $open = ($i === 1 && !empty($attributes['openFirst'])) ? ' open' : '';
            $items .= sprintf(
                '<details class="fluxstack-accordion__item"%s><summary class="fluxstack-accordion__question">%s</summary><div class="fluxstack-accordion__answer">%s</div></details>',
                $open, esc_html($q), wp_kses_post($a)
            );
        }

        return sprintf('<div %s>%s</div>', $wrapper, $items);
    }

    public function editorStyles(): void {
        wp_enqueue_style('fluxstack-accordion', get_theme_file_uri('modules/accordion-block/style.css'));
    }
};
