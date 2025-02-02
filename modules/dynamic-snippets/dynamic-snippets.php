<?php
/**
 * Dynamic Snippets
 *
 * @package FluxStack
 */

class FS_Dynamic_Snippets {
    public static function init() {
        // Load global snippets
        require_once dirname(__FILE__) . '/global/global.php';

        // Register hooks
        add_filter('bricks/dynamic_data/register_snippets', array(__CLASS__, 'register_snippets'));
        add_filter('bricks/dynamic_data/register_categories', array(__CLASS__, 'register_categories'));
    }

    /**
     * Register Dynamic Snippets for Bricks
     *
     * @param array $snippets Array of registered snippets.
     * @return array Modified snippets array.
     */
    public static function register_snippets($snippets) {
        // Add custom snippets here
        $snippets['fluxstack_social_links'] = array(
            'name'        => 'social_links',
            'label'       => __('Social Links', 'fluxstack'),
            'category'    => 'fluxstack',
            'render'      => array(__CLASS__, 'render_social_links')
        );

        $snippets['fluxstack_copyright'] = array(
            'name'        => 'copyright',
            'label'       => __('Copyright', 'fluxstack'),
            'category'    => 'fluxstack',
            'render'      => array(__CLASS__, 'render_copyright')
        );

        return $snippets;
    }

    /**
     * Render social links snippet
     *
     * @return string Rendered HTML
     */
    public static function render_social_links() {
        $social_links = FS_Utils::get_theme_option('social_media_links');
        if (!$social_links) {
            return '';
        }

        ob_start();
        ?>
        <div class="fluxstack-social-links">
            <?php foreach ($social_links as $link) : ?>
                <?php if (!empty($link['url']) && !empty($link['platform'])) : ?>
                    <a href="<?php echo esc_url($link['url']); ?>" 
                       class="social-link"
                       target="_blank"
                       rel="noopener noreferrer">
                        <i class="<?php echo esc_attr($link['platform']); ?>"></i>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render copyright snippet
     *
     * @return string Rendered HTML
     */
    public static function render_copyright() {
        $copyright_text = FS_Utils::get_theme_option('copyright_text', '© [year] [site_name]. All rights reserved.');
        $site_name = get_bloginfo('name');
        $year = date('Y');
        
        $replacements = array(
            '[year]' => $year,
            '[site_name]' => $site_name
        );
        
        return wp_kses_post(str_replace(
            array_keys($replacements),
            array_values($replacements),
            $copyright_text
        ));
    }

    /**
     * Register Dynamic Tags Category
     *
     * @param array $categories Array of registered categories.
     * @return array Modified categories array.
     */
    public static function register_categories($categories) {
        $categories['fluxstack'] = array(
            'label' => __('FluxStack', 'fluxstack'),
            'icon'  => 'fas fa-cube',
        );
        
        return $categories;
    }
}

// Initialize the dynamic snippets module
FS_Dynamic_Snippets::init();
