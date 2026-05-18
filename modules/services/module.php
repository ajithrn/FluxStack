<?php
use App\Modules\CptModule;
return new class extends CptModule {
    public function id(): string { return 'services'; }
    public function name(): string { return 'Services'; }
    public function description(): string { return 'Services management with ordering and thumbnails.'; }
    public function postType(): string { return 'service'; }
    public function labels(): array { return ['name' => 'Services', 'singular_name' => 'Service', 'menu_name' => 'Services', 'all_items' => 'All Services', 'add_new_item' => 'Add New Service', 'edit_item' => 'Edit Service', 'not_found' => 'Not found']; }
    public function postTypeArgs(): array { return ['menu_icon' => 'dashicons-admin-generic', 'supports' => ['title', 'thumbnail', 'revisions', 'page-attributes'], 'rewrite' => ['slug' => 'services'], 'show_in_rest' => false]; }
};
