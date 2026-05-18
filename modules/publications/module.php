<?php

use App\Modules\CptModule;

return new class extends CptModule
{
    public function id(): string { return 'publications'; }
    public function name(): string { return 'Publications'; }
    public function description(): string { return 'Publication management with types, PDF support, and date-based organization.'; }
    public function postType(): string { return 'publication'; }

    public function labels(): array
    {
        return [
            'name' => 'Publications',
            'singular_name' => 'Publication',
            'menu_name' => 'Publications',
            'all_items' => 'All Publications',
            'add_new_item' => 'Add New Publication',
            'edit_item' => 'Edit Publication',
            'not_found' => 'Not found',
        ];
    }

    public function postTypeArgs(): array
    {
        return [
            'menu_icon' => 'dashicons-media-document',
            'supports' => ['title', 'thumbnail', 'custom-fields'],
            'rewrite' => ['slug' => 'publication'],
            'public' => false,
            'show_ui' => true,
            'publicly_queryable' => false,
            'has_archive' => true,
            'show_in_rest' => false,
        ];
    }

    public function taxonomies(): array
    {
        return [
            'publication_type' => [
                'labels' => [
                    'name' => 'Publication Types',
                    'singular_name' => 'Publication Type',
                ],
                'args' => [
                    'rewrite' => ['slug' => 'publication-type'],
                    'show_admin_column' => false,
                    'show_in_rest' => false,
                ],
            ],
        ];
    }
};
