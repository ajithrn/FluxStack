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
            'show_in_rest' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
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
}
