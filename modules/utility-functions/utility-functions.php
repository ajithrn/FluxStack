<?php
/**
 * Utility Functions
 *
 * @package FluxStack
 */

class FS_Utils {
    public static function init() {
        // Initialize any hooks or filters if needed
    }

    /**
     * Get theme option value
     *
     * @param string $option_name Option name.
     * @param mixed  $default     Default value.
     * @return mixed Option value.
     */
    public static function get_theme_option($option_name, $default = '') {
        return FS_Theme_Options::get_option($option_name, $default);
    }

    /**
     * Get formatted date
     *
     * @param string $date   Date string.
     * @param string $format Date format (default: 'F j, Y').
     * @return string Formatted date.
     */
    public static function format_date($date, $format = 'F j, Y') {
        return date($format, strtotime($date));
    }

    /**
     * Get post thumbnail URL
     *
     * @param int    $post_id Post ID.
     * @param string $size    Image size (default: 'full').
     * @return string|false Image URL or false if no thumbnail.
     */
    public static function get_thumbnail_url($post_id = null, $size = 'full') {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        if (has_post_thumbnail($post_id)) {
            return get_the_post_thumbnail_url($post_id, $size);
        }
        
        return false;
    }

    /**
     * Get excerpt with custom length
     *
     * @param int    $length   Excerpt length.
     * @param string $more     More text.
     * @param int    $post_id  Post ID.
     * @return string Modified excerpt.
     */
    public static function get_excerpt($length = 55, $more = '...', $post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        $post = get_post($post_id);
        if (!$post) {
            return '';
        }

        if (has_excerpt($post_id)) {
            return get_the_excerpt($post_id);
        }

        $excerpt = strip_shortcodes($post->post_content);
        $excerpt = wp_strip_all_tags($excerpt);
        $excerpt = wp_trim_words($excerpt, $length, $more);

        return $excerpt;
    }

    /**
     * Check if current page is using Bricks template
     *
     * @return boolean True if using Bricks template.
     */
    public static function is_bricks_template() {
        return FS_Bricks::is_bricks_template();
    }

    /**
     * Get image data with fallback
     *
     * @param int    $image_id Image ID.
     * @param string $size     Image size.
     * @return array Image data array.
     */
    public static function get_image_data($image_id, $size = 'full') {
        $image = wp_get_attachment_image_src($image_id, $size);
        
        return array(
            'url'    => $image ? $image[0] : '',
            'width'  => $image ? $image[1] : '',
            'height' => $image ? $image[2] : '',
            'alt'    => get_post_meta($image_id, '_wp_attachment_image_alt', true),
        );
    }

    /**
     * Clean phone number for standardized format
     *
     * @param string $phone Phone number.
     * @return string Cleaned phone number.
     */
    public static function clean_phone($phone) {
        return preg_replace('/[^0-9+]/', '', $phone);
    }

    /**
     * Get post meta with default value
     *
     * @param string $key     Meta key.
     * @param int    $post_id Post ID.
     * @param mixed  $default Default value.
     * @return mixed Meta value.
     */
    public static function get_post_meta($key, $post_id = null, $default = '') {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        $value = get_post_meta($post_id, $key, true);
        return $value !== '' ? $value : $default;
    }
}

// Initialize the utils module
FS_Utils::init();

// Function wrappers for backward compatibility
function fluxstack_get_theme_option($option_name, $default = '') {
    return FS_Utils::get_theme_option($option_name, $default);
}

function fluxstack_format_date($date, $format = 'F j, Y') {
    return FS_Utils::format_date($date, $format);
}

function fluxstack_get_thumbnail_url($post_id = null, $size = 'full') {
    return FS_Utils::get_thumbnail_url($post_id, $size);
}

function fluxstack_get_excerpt($length = 55, $more = '...', $post_id = null) {
    return FS_Utils::get_excerpt($length, $more, $post_id);
}

function fluxstack_is_bricks_template() {
    return FS_Utils::is_bricks_template();
}
