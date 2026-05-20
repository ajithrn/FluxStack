<?php

namespace App\Modules;

abstract class BaseModule
{
    /** Module unique identifier */
    abstract public function id(): string;

    /** Human-readable module name */
    abstract public function name(): string;

    /** Module description */
    abstract public function description(): string;

    /** Module category (block, cpt, feature, utility) */
    abstract public function category(): string;

    /** Register hooks, CPTs, taxonomies, etc. */
    abstract public function register(): void;

    /** Boot/initialize the module (runs after all modules registered) */
    public function boot(): void {}

    /** Check if module is enabled */
    public function isEnabled(): bool
    {
        // Core modules are always enabled
        $config = file_exists(get_theme_file_path('config/modules.php'))
            ? require get_theme_file_path('config/modules.php')
            : [];
        if (in_array($this->id(), $config['core'] ?? [])) {
            return true;
        }

        $modules = get_option('fluxstack_modules', []);
        return $modules[$this->id()] ?? $this->enabledByDefault();
    }

    /** Whether module is enabled by default */
    public function enabledByDefault(): bool
    {
        return false;
    }

    /** Module dependencies (other module IDs) */
    public function dependencies(): array
    {
        return [];
    }

    /** Get module directory path */
    public function path(): string
    {
        return get_theme_file_path("modules/{$this->id()}");
    }

    /** Get module directory URI */
    public function uri(): string
    {
        return get_theme_file_uri("modules/{$this->id()}");
    }

    /**
     * Check if we're rendering inside the block editor (ServerSideRender REST call).
     */
    protected function isEditorPreview(): bool
    {
        return defined('REST_REQUEST') && REST_REQUEST;
    }

    /**
     * Render a placeholder preview for the block editor when attributes are empty.
     *
     * @param string $blockClass  The block's BEM base class (e.g. 'fluxstack-cta')
     * @param string $title       Placeholder title text
     * @param string $description Placeholder description/hint
     * @param string $sampleHtml  Optional sample HTML showing what the block looks like
     */
    protected function renderPlaceholder(string $blockClass, string $title, string $description, string $sampleHtml = ''): string
    {
        $wrapper = get_block_wrapper_attributes([
            'class' => $blockClass . ' fluxstack-block-placeholder',
        ]);

        $sample = $sampleHtml ? '<div class="fluxstack-block-placeholder__sample">' . $sampleHtml . '</div>' : '';

        // Inline styles ensure they render in the editor canvas (iframe in WP 6.x+/7.x)
        static $stylesRendered = false;
        $styles = '';
        if (!$stylesRendered) {
            $stylesRendered = true;
            $styles = '<style>
.fluxstack-block-placeholder{border:2px dashed #3858e9;border-radius:.5rem;background:rgba(56,88,233,.04);padding:2rem}
.fluxstack-block-placeholder__inner{display:flex;flex-direction:column;gap:1.5rem}
.fluxstack-block-placeholder__sample{opacity:.5;pointer-events:none}
.fluxstack-block-placeholder__meta{text-align:center;padding-top:1rem;border-top:1px solid rgba(56,88,233,.12)}
.fluxstack-block-placeholder__title{font-size:1rem;font-weight:600;margin:0 0 .25rem;color:#3858e9}
.fluxstack-block-placeholder__desc{font-size:.85rem;margin:0 0 .75rem;opacity:.7;color:inherit}
.fluxstack-block-placeholder__hint{display:inline-flex;align-items:center;gap:.35rem;font-size:.8rem;font-weight:500;color:#3858e9;background:rgba(56,88,233,.08);padding:.4rem .85rem;border-radius:1rem}
.fluxstack-block-placeholder__hint .dashicons{font-size:14px;width:14px;height:14px;line-height:14px}
</style>';
        }

        return sprintf(
            '%s<div %s><div class="fluxstack-block-placeholder__inner">%s<div class="fluxstack-block-placeholder__meta"><p class="fluxstack-block-placeholder__title">%s</p><p class="fluxstack-block-placeholder__desc">%s</p><span class="fluxstack-block-placeholder__hint"><span class="dashicons dashicons-edit"></span> %s</span></div></div></div>',
            $styles,
            $wrapper,
            $sample,
            esc_html($title),
            esc_html($description),
            esc_html__('Edit in the sidebar panel', 'fluxstack')
        );
    }
}
