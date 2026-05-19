<?php

namespace App\Providers;

use App\Modules\ModuleManager;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ModuleManager::class, function () {
            return new ModuleManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $manager = $this->app->make(ModuleManager::class);

        // Auto-discover modules from the modules directory
        $modulesPath = get_theme_file_path('modules');
        $manager->discover($modulesPath);

        // Boot all enabled modules
        $manager->boot();

        // Register the block category
        add_filter('block_categories_all', function ($categories) {
            return array_merge(
                [
                    [
                        'slug' => 'fluxstack',
                        'title' => __('FluxStack Blocks', 'fluxstack'),
                    ],
                ],
                $categories
            );
        });

        // Enqueue shared block placeholder styles in the editor
        add_action('enqueue_block_editor_assets', function () {
            wp_register_style('fluxstack-block-placeholder', false);
            wp_enqueue_style('fluxstack-block-placeholder');
            wp_add_inline_style('fluxstack-block-placeholder', $this->getPlaceholderCss());
        });
    }

    /**
     * Inline CSS for block editor placeholders.
     */
    private function getPlaceholderCss(): string
    {
        return <<<'CSS'
/* --- Placeholder canvas styles (also inlined in render output) --- */
.fluxstack-block-placeholder {
    border: 2px dashed #3858e9;
    border-radius: 0.5rem;
    background: rgba(56, 88, 233, 0.04);
    padding: 2rem;
}
.fluxstack-block-placeholder__inner {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}
.fluxstack-block-placeholder__sample {
    opacity: 0.5;
    pointer-events: none;
}
.fluxstack-block-placeholder__meta {
    text-align: center;
    padding-top: 1rem;
    border-top: 1px solid rgba(56, 88, 233, 0.12);
}
.fluxstack-block-placeholder__title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 0.25rem;
    color: #3858e9;
}
.fluxstack-block-placeholder__desc {
    font-size: 0.85rem;
    margin: 0 0 0.75rem;
    opacity: 0.7;
    color: inherit;
}
.fluxstack-block-placeholder__hint {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.8rem;
    font-weight: 500;
    color: #3858e9;
    background: rgba(56, 88, 233, 0.08);
    padding: 0.4rem 0.85rem;
    border-radius: 1rem;
}
.fluxstack-block-placeholder__hint .dashicons {
    font-size: 14px;
    width: 14px;
    height: 14px;
    line-height: 14px;
}
CSS;
    }
}
