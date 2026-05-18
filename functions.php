<?php

use App\Providers\ModuleServiceProvider;
use App\Providers\ThemeServiceProvider;
use Roots\Acorn\Application;

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our theme. We will simply require it into the script here so that we
| don't have to worry about manually loading any of our classes later on.
|
*/

if (! file_exists($composer = __DIR__.'/vendor/autoload.php')) {
    wp_die(__('Error locating autoloader. Please run <code>composer install</code>.', 'sage'));
}

require $composer;

/*
|--------------------------------------------------------------------------
| Register The Bootloader
|--------------------------------------------------------------------------
|
| The first thing we will do is schedule a new Acorn application container
| to boot when WordPress is finished loading the theme. The application
| serves as the "glue" for all the components of Laravel and is
| the IoC container for the system binding all of the various parts.
|
*/

Application::configure()
    ->withProviders([
        ThemeServiceProvider::class,
        ModuleServiceProvider::class,
    ])
    ->boot();

/*
|--------------------------------------------------------------------------
| Register Sage Theme Files
|--------------------------------------------------------------------------
|
| Out of the box, Sage ships with categorically named theme files
| containing common functionality and setup to be bootstrapped with your
| theme. Simply add (or remove) files from the array below to change what
| is registered alongside Sage.
|
*/

collect(['setup', 'filters', 'helpers'])
    ->each(function ($file) {
        if (! locate_template($file = "app/{$file}.php", true, true)) {
            wp_die(
                /* translators: %s is replaced with the relative file path */
                sprintf(__('Error locating <code>%s</code> for inclusion.', 'sage'), $file)
            );
        }
    });

/*
|--------------------------------------------------------------------------
| Boot Module System (Fallback)
|--------------------------------------------------------------------------
|
| If Acorn's service container doesn't boot the modules (e.g. missing
| mu-plugin or environment issue), this ensures modules still load.
|
*/

add_action('after_setup_theme', function () {
    // Skip if ModuleServiceProvider already booted via Acorn
    if (function_exists('app') && app()->bound(\App\Modules\ModuleManager::class)) {
        return;
    }

    $manager = new \App\Modules\ModuleManager();
    $modulesPath = get_theme_file_path('modules');
    $manager->discover($modulesPath);
    $manager->boot();

    // Make manager accessible globally and via container
    $GLOBALS['fluxstack_manager'] = $manager;
    if (function_exists('app')) {
        try {
            app()->instance(\App\Modules\ModuleManager::class, $manager);
        } catch (\Throwable $e) {
            // Container not ready yet, global fallback is fine
        }
    }

    // Register block category
    add_filter('block_categories_all', function ($categories) {
        return array_merge(
            [['slug' => 'fluxstack', 'title' => __('FluxStack Blocks', 'fluxstack')]],
            $categories
        );
    });
}, 5);
