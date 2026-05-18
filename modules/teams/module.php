<?php
use App\Modules\CptModule;
return new class extends CptModule {
    public function id(): string { return 'teams'; }
    public function name(): string { return 'Teams'; }
    public function description(): string { return 'Team member management with profiles, roles, and categories.'; }
    public function postType(): string { return 'team_member'; }
    public function labels(): array { return ['name' => 'Team Members', 'singular_name' => 'Team Member', 'menu_name' => 'Team', 'all_items' => 'All Members', 'add_new_item' => 'Add New Member', 'edit_item' => 'Edit Member', 'not_found' => 'Not found']; }
    public function postTypeArgs(): array { return ['menu_icon' => 'dashicons-groups', 'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'], 'rewrite' => ['slug' => 'team']]; }
    public function taxonomies(): array { return ['team_category' => ['labels' => ['name' => 'Team Categories', 'singular_name' => 'Team Category'], 'args' => ['rewrite' => ['slug' => 'team-category']]]]; }
};
