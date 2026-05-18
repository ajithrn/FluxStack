<?php

namespace App\Modules;

abstract class BlockModule extends BaseModule
{
    public function category(): string
    {
        return 'block';
    }

    /** Block name (namespace/block-name) */
    abstract public function blockName(): string;

    public function register(): void
    {
        add_action('init', [$this, 'registerBlock']);
    }

    public function registerBlock(): void
    {
        $block_path = $this->path();

        // Register using block.json in module directory
        if (file_exists($block_path . '/block.json')) {
            register_block_type($block_path);
        }
    }
}
