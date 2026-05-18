<?php

use App\Modules\CptModule;

return new class extends CptModule
{
    public function id(): string { return 'portfolio'; }
    public function name(): string { return 'Portfolio'; }
    public function description(): string { return 'Portfolio management with project details, types, and gallery.'; }
    public function postType(): string { return 'portfolio'; }

    public function labels(): array
    {
        return [
            'name' => 'Portfolio',
            'singular_name' => 'Portfolio Item',
            'menu_name' => 'Portfolio',
            'all_items' => 'All Portfolio',
            'add_new_item' => 'Add New Portfolio Item',
            'edit_item' => 'Edit Portfolio Item',
            'not_found' => 'Not found',
        ];
    }

    public function postTypeArgs(): array
    {
        return [
            'menu_icon' => 'dashicons-portfolio',
            'supports' => ['title', 'thumbnail', 'excerpt', 'revisions', 'page-attributes'],
            'rewrite' => ['slug' => 'portfolio'],
            'show_in_rest' => false,
        ];
    }

    public function taxonomies(): array
    {
        return [
            'portfolio_type' => [
                'labels' => [
                    'name' => 'Portfolio Types',
                    'singular_name' => 'Portfolio Type',
                ],
                'args' => [
                    'rewrite' => ['slug' => 'portfolio-type'],
                    'show_in_rest' => true,
                ],
            ],
        ];
    }
};
