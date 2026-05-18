<?php
use App\Modules\CptModule;
return new class extends CptModule {
    public function id(): string { return 'testimonials'; }
    public function name(): string { return 'Testimonials'; }
    public function description(): string { return 'Testimonial management with ratings, categories, and admin columns.'; }
    public function postType(): string { return 'testimonial'; }
    public function labels(): array { return ['name' => 'Testimonials', 'singular_name' => 'Testimonial', 'menu_name' => 'Testimonials', 'all_items' => 'All Testimonials', 'add_new_item' => 'Add New Testimonial', 'edit_item' => 'Edit Testimonial', 'not_found' => 'Not found']; }
    public function postTypeArgs(): array { return ['menu_icon' => 'dashicons-format-quote', 'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'], 'rewrite' => ['slug' => 'testimonial']]; }
    public function taxonomies(): array { return ['testimonial_category' => ['labels' => ['name' => 'Categories', 'singular_name' => 'Category'], 'args' => ['rewrite' => ['slug' => 'testimonial-category']]]]; }
    public function register(): void {
        parent::register();
        add_filter('manage_testimonial_posts_columns', function($cols) { $n = []; foreach ($cols as $k => $v) { $n[$k] = $v; if ($k === 'title') $n['rating'] = 'Rating'; } return $n; });
        add_action('manage_testimonial_posts_custom_column', function($col, $id) { if ($col === 'rating') { $r = get_field('testimonial_rating', $id); if ($r) echo str_repeat('★', intval($r)) . str_repeat('☆', 5 - intval($r)); } }, 10, 2);
    }
};
