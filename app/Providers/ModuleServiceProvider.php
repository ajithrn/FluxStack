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
    }
}
