<?php

namespace App;

use App\Modules\ModuleManager;

/**
 * Get the module manager instance.
 */
function modules(): ModuleManager
{
    return app(ModuleManager::class);
}

/**
 * Get a theme option value (shorthand).
 */
function theme_option(string $key, mixed $default = ''): mixed
{
    if (function_exists('get_field')) {
        return get_field($key, 'option') ?? $default;
    }
    return $default;
}

/**
 * Get formatted excerpt with custom length.
 */
function get_excerpt(int $length = 55, string $more = '...', ?int $postId = null): string
{
    if (! $postId) {
        $postId = get_the_ID();
    }

    $post = get_post($postId);
    if (! $post) {
        return '';
    }

    if (has_excerpt($postId)) {
        return get_the_excerpt($postId);
    }

    $excerpt = strip_shortcodes($post->post_content);
    $excerpt = wp_strip_all_tags($excerpt);
    return wp_trim_words($excerpt, $length, $more);
}

/**
 * Get post thumbnail URL with fallback.
 */
function get_thumbnail_url(?int $postId = null, string $size = 'full'): string|false
{
    if (! $postId) {
        $postId = get_the_ID();
    }

    if (has_post_thumbnail($postId)) {
        return get_the_post_thumbnail_url($postId, $size);
    }

    return false;
}

/**
 * Get a site setting value.
 */
function site_setting(string $key, mixed $default = ''): mixed
{
    static $settings = null;

    if ($settings === null) {
        $settings = get_option('fluxstack_site_settings', []);
        if (!is_array($settings)) {
            $settings = [];
        }
    }

    return $settings[$key] ?? $default;
}
