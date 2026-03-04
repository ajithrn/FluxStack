<?php
/**
 * Social Links snippet
 * Renders social media links from Theme Options.
 */
return array(
    'name'     => 'social_links',
    'label'    => __('Social Links', 'fluxstack'),
    'category' => 'fluxstack',
    'render'   => function() {
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
    },
);
