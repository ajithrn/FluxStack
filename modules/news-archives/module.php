<?php

use App\Modules\BaseModule;

return new class extends BaseModule
{
    const YEAR_TAXONOMY = 'news_year';

    public function id(): string { return 'news-archives'; }
    public function name(): string { return 'News Archives'; }
    public function description(): string { return 'Year-based news organization with auto-assignment and archive browsing.'; }
    public function category(): string { return 'feature'; }

    public function register(): void
    {
        add_action('init', [$this, 'registerYearTaxonomy'], 0);
        add_action('save_post', [$this, 'assignPostToYear'], 10, 3);
    }

    public function registerYearTaxonomy(): void
    {
        register_taxonomy(self::YEAR_TAXONOMY, ['post'], [
            'hierarchical' => true,
            'labels' => [
                'name' => 'News Years',
                'singular_name' => 'News Year',
            ],
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'news/year'],
            'show_in_rest' => true,
        ]);
    }

    public function assignPostToYear(int $postId, \WP_Post $post, bool $update): void
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if ($post->post_type !== 'post' || wp_is_post_revision($postId) || $post->post_status !== 'publish') {
            return;
        }
        $year = date('Y', strtotime($post->post_date));
        if (! term_exists($year, self::YEAR_TAXONOMY)) {
            wp_insert_term($year, self::YEAR_TAXONOMY, ['slug' => $year]);
        }
        wp_set_object_terms($postId, $year, self::YEAR_TAXONOMY, false);
    }
};
