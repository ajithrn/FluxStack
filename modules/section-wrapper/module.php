<?php
/**
 * Section Wrapper Block — PHP-only (autoRegister)
 * Generic content section with background, spacing, and width controls.
 */
use App\Modules\BaseModule;

return new class extends BaseModule {
    public function id(): string { return 'section-wrapper'; }
    public function name(): string { return 'Section Wrapper'; }
    public function description(): string { return 'Generic section with background color, spacing, and width options.'; }
    public function category(): string { return 'block'; }
    public function enabledByDefault(): bool { return true; }

    public function register(): void {
        add_action('init', [$this, 'registerBlock']);
        add_action('enqueue_block_editor_assets', [$this, 'editorStyles']);
    }

    public function registerBlock(): void {
        register_block_type('fluxstack/section', [
            'title'    => __('Section', 'fluxstack'),
            'category' => 'fluxstack',
            'icon'     => 'align-center',
            'attributes' => [
                'heading'    => ['type' => 'string', 'default' => '', 'label' => 'Section Heading'],
                'subheading' => ['type' => 'string', 'default' => '', 'label' => 'Subheading'],
                'width'      => ['type' => 'string', 'enum' => ['Default', 'Wide', 'Full'], 'default' => 'Default', 'label' => 'Content Width'],
                'paddingSize' => ['type' => 'string', 'enum' => ['Small', 'Medium', 'Large', 'Extra Large'], 'default' => 'Medium', 'label' => 'Vertical Padding'],
            ],
            'supports' => [
                'autoRegister' => true,
                'align'        => ['wide', 'full'],
                'color'        => ['text' => true, 'background' => true],
                'spacing'      => ['padding' => true, 'margin' => true],
            ],
            'render_callback' => [$this, 'render'],
        ]);

        wp_enqueue_block_style('fluxstack/section', [
            'handle' => 'fluxstack-section',
            'src'    => get_theme_file_uri('modules/section-wrapper/style.css'),
            'path'   => get_theme_file_path('modules/section-wrapper/style.css'),
        ]);
    }

    public function render(array $attributes): string {
        $padding = match($attributes['paddingSize'] ?? 'Medium') {
            'Small' => '2rem 0', 'Large' => '6rem 0', 'Extra Large' => '8rem 0', default => '4rem 0',
        };
        $width = match($attributes['width'] ?? 'Default') {
            'Wide' => 'max-width:75rem;', 'Full' => 'max-width:100%;', default => 'max-width:48rem;',
        };

        $wrapper = get_block_wrapper_attributes([
            'class' => 'fluxstack-section',
            'style' => 'padding:' . $padding . ';',
        ]);

        $header = '';
        if (!empty($attributes['heading'])) {
            $header .= '<h2 class="fluxstack-section__heading">' . esc_html($attributes['heading']) . '</h2>';
        }
        if (!empty($attributes['subheading'])) {
            $header .= '<p class="fluxstack-section__subheading">' . esc_html($attributes['subheading']) . '</p>';
        }
        $headerWrap = $header ? '<div class="fluxstack-section__header">' . $header . '</div>' : '';

        return sprintf('<section %s><div class="fluxstack-section__inner" style="%s margin:0 auto;">%s</div></section>', $wrapper, $width, $headerWrap);
    }

    public function editorStyles(): void {
        wp_enqueue_style('fluxstack-section', get_theme_file_uri('modules/section-wrapper/style.css'));
    }
};
