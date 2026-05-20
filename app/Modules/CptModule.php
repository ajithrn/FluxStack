<?php

namespace App\Modules;

abstract class CptModule extends BaseModule
{
    public function category(): string
    {
        return 'cpt';
    }

    /** Post type slug */
    abstract public function postType(): string;

    /** Post type labels */
    abstract public function labels(): array;

    /** Post type arguments (merged with defaults) */
    public function postTypeArgs(): array
    {
        return [];
    }

    /** Taxonomies to register: [slug => [labels, args]] */
    public function taxonomies(): array
    {
        return [];
    }

    public function register(): void
    {
        add_action('init', [$this, 'registerPostType'], 0);
        add_action('init', [$this, 'registerTaxonomies'], 0);

        // ACF JSON support
        add_filter('acf/settings/load_json', [$this, 'addAcfJsonLoadPoint']);
        add_action('acf/update_field_group', [$this, 'handleFieldGroupSave'], 1, 1);

        // Register blocks that live inside this module
        add_action('init', [$this, 'registerModuleBlocks']);
    }

    /**
     * Register the custom post type.
     */
    public function registerPostType(): void
    {
        $defaults = [
            'labels' => $this->labels(),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => false,
            'has_archive' => true,
            'supports' => ['title', 'thumbnail', 'excerpt', 'custom-fields'],
            'menu_position' => 20,
            'capability_type' => 'post',
        ];

        $args = array_merge($defaults, $this->postTypeArgs());
        register_post_type($this->postType(), $args);
    }

    /**
     * Register taxonomies for this CPT.
     */
    public function registerTaxonomies(): void
    {
        foreach ($this->taxonomies() as $slug => $config) {
            $defaults = [
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_rest' => true,
                'query_var' => true,
            ];

            $args = array_merge($defaults, $config['args'] ?? []);
            $args['labels'] = $config['labels'] ?? [];

            register_taxonomy($slug, [$this->postType()], $args);
        }
    }

    /**
     * Register blocks that live inside this module's blocks/ directory.
     */
    public function registerModuleBlocks(): void
    {
        $blocksDir = $this->path() . '/blocks';

        if (! is_dir($blocksDir)) {
            return;
        }

        $blockFolders = glob($blocksDir . '/*', GLOB_ONLYDIR);

        foreach ($blockFolders as $blockFolder) {
            // Register using block.json if it exists
            if (file_exists($blockFolder . '/block.json')) {
                register_block_type($blockFolder);
            }
        }
    }

    /**
     * Add ACF JSON load point for this module.
     */
    public function addAcfJsonLoadPoint(array $paths): array
    {
        $acfPath = $this->path() . '/acf-json';
        if (is_dir($acfPath)) {
            $paths[] = $acfPath;
        }
        return $paths;
    }

    /**
     * Handle ACF field group save — override in child to specify group keys.
     */
    public function handleFieldGroupSave(array $group): void
    {
        // Override in child modules to save specific groups to module's acf-json
    }

    /**
     * Boot the module — scaffold view templates if they don't exist.
     *
     * Copies skeleton templates from modules/{id}/views/ to resources/views/
     * on first activation. Templates in resources/views/ are the ones Sage
     * uses — customize them per project. The module's views/ folder holds
     * the defaults/skeletons only.
     */
    public function boot(): void
    {
        // Only scaffold once per module — skip on subsequent requests
        $flag = 'fluxstack_scaffolded_' . $this->id();
        if (! get_option($flag)) {
            $this->scaffoldViews();
            update_option($flag, true, false);
        }
    }

    /**
     * Copy skeleton templates from module to theme views if not already present.
     */
    protected function scaffoldViews(): void
    {
        $skeletonsDir = $this->path() . '/views';

        if (! is_dir($skeletonsDir)) {
            return;
        }

        $themeViewsDir = get_theme_file_path('resources/views');
        $files = glob($skeletonsDir . '/*.blade.php');

        foreach ($files as $file) {
            $filename = basename($file);
            $destination = $themeViewsDir . '/' . $filename;

            // Only copy if the theme doesn't already have this template
            if (! file_exists($destination)) {
                copy($file, $destination);
            }
        }

        // Also scaffold partials
        $partialsDir = $skeletonsDir . '/partials';
        if (is_dir($partialsDir)) {
            $themePartialsDir = $themeViewsDir . '/partials';
            $partials = glob($partialsDir . '/*.blade.php');

            foreach ($partials as $file) {
                $filename = basename($file);
                $destination = $themePartialsDir . '/' . $filename;

                if (! file_exists($destination)) {
                    copy($file, $destination);
                }
            }
        }

        // Scaffold CSS files from module styles/ to resources/css/modules/
        $stylesDir = $this->path() . '/styles';
        if (is_dir($stylesDir)) {
            $themeCssDir = get_theme_file_path('resources/css/modules');
            $cssFiles = glob($stylesDir . '/*.css');

            foreach ($cssFiles as $file) {
                $filename = basename($file);
                $destination = $themeCssDir . '/' . $filename;

                if (! file_exists($destination)) {
                    copy($file, $destination);
                }
            }
        }
    }
}
