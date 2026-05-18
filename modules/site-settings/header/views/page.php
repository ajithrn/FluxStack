<div class="fluxstack-app">
    <div class="fluxstack-header">
        <div class="fluxstack-header__left"><h1 class="fluxstack-header__title"><?php esc_html_e('Header', 'fluxstack'); ?></h1></div>
        <div class="fluxstack-header__right">
            <button type="button" class="fluxstack-btn fluxstack-btn--primary" id="fluxstack-save" data-action="fluxstack_save_site_settings">
                <span class="fluxstack-btn__text"><?php esc_html_e('Save Changes', 'fluxstack'); ?></span>
                <span class="dashicons dashicons-saved"></span>
            </button>
        </div>
    </div>
    <div class="fluxstack-toast" id="fluxstack-toast" hidden></div>

    <section class="fluxstack-section">
        <h2 class="fluxstack-section__title"><?php esc_html_e('Layout & Style', 'fluxstack'); ?></h2>
        <p class="fluxstack-section__desc"><?php esc_html_e('Control how the main site header looks and behaves across the site.', 'fluxstack'); ?></p>
        <div class="fluxstack-settings-form">
            <div class="fluxstack-field">
                <label class="fluxstack-field__label"><?php esc_html_e('Header Layout', 'fluxstack'); ?></label>
                <select class="fluxstack-field__select" name="fluxstack_site_settings[header_layout]">
                    <option value="default" <?php selected($settings['header_layout'] ?? '', 'default'); ?>>Logo Left / Nav Right</option>
                    <option value="centered" <?php selected($settings['header_layout'] ?? '', 'centered'); ?>>Centered Logo</option>
                    <option value="stacked" <?php selected($settings['header_layout'] ?? '', 'stacked'); ?>>Stacked (Logo above Nav)</option>
                </select>
                <span class="fluxstack-field__help"><?php esc_html_e('Choose the arrangement of logo and navigation elements.', 'fluxstack'); ?></span>
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-field__label"><?php esc_html_e('Background Color', 'fluxstack'); ?></label>
                <input type="color" class="fluxstack-field__color" name="fluxstack_site_settings[header_bg_color]" value="<?php echo esc_attr($settings['header_bg_color'] ?? '#ffffff'); ?>">
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-field__label"><?php esc_html_e('Behavior', 'fluxstack'); ?></label>
                <div style="display:flex;flex-direction:column;gap:0.75rem;">
                    <label class="fluxstack-checkbox"><input type="checkbox" name="fluxstack_site_settings[header_sticky]" value="1" <?php checked(!empty($settings['header_sticky'])); ?>> <?php esc_html_e('Sticky on scroll', 'fluxstack'); ?></label>
                    <label class="fluxstack-checkbox"><input type="checkbox" name="fluxstack_site_settings[header_transparent]" value="1" <?php checked(!empty($settings['header_transparent'])); ?>> <?php esc_html_e('Transparent over hero sections', 'fluxstack'); ?></label>
                    <label class="fluxstack-checkbox"><input type="checkbox" name="fluxstack_site_settings[header_shadow]" value="1" <?php checked(!empty($settings['header_shadow'])); ?>> <?php esc_html_e('Show drop shadow', 'fluxstack'); ?></label>
                </div>
            </div>
        </div>
    </section>

    <section class="fluxstack-section">
        <h2 class="fluxstack-section__title"><?php esc_html_e('Top Bar', 'fluxstack'); ?></h2>
        <p class="fluxstack-section__desc"><?php esc_html_e('A slim utility bar above the main header. Useful for contact info, announcements, or secondary navigation.', 'fluxstack'); ?></p>
        <div class="fluxstack-settings-form">
            <div class="fluxstack-field">
                <label class="fluxstack-checkbox"><input type="checkbox" name="fluxstack_site_settings[header_topbar_enabled]" value="1" <?php checked(!empty($settings['header_topbar_enabled'])); ?>> <?php esc_html_e('Enable top bar', 'fluxstack'); ?></label>
            </div>
            <div class="fluxstack-field fluxstack-field--wide">
                <label class="fluxstack-field__label"><?php esc_html_e('Left Content', 'fluxstack'); ?></label>
                <input type="text" class="fluxstack-field__input" name="fluxstack_site_settings[header_topbar_left]" value="<?php echo esc_attr($settings['header_topbar_left'] ?? ''); ?>" placeholder="<?php esc_attr_e('Phone: (555) 000-0000 | Email: hello@example.com', 'fluxstack'); ?>">
                <span class="fluxstack-field__help"><?php esc_html_e('Displayed on the left side. Supports basic HTML and pipe (|) separators.', 'fluxstack'); ?></span>
            </div>
            <div class="fluxstack-field fluxstack-field--wide">
                <label class="fluxstack-field__label"><?php esc_html_e('Right Content', 'fluxstack'); ?></label>
                <input type="text" class="fluxstack-field__input" name="fluxstack_site_settings[header_topbar_right]" value="<?php echo esc_attr($settings['header_topbar_right'] ?? ''); ?>" placeholder="<?php esc_attr_e('Mon-Fri: 9am - 5pm', 'fluxstack'); ?>">
                <span class="fluxstack-field__help"><?php esc_html_e('Displayed on the right side. Leave empty to center the left content.', 'fluxstack'); ?></span>
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-field__label"><?php esc_html_e('Background', 'fluxstack'); ?></label>
                <input type="color" class="fluxstack-field__color" name="fluxstack_site_settings[header_topbar_bg]" value="<?php echo esc_attr($settings['header_topbar_bg'] ?? '#1e293b'); ?>">
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-field__label"><?php esc_html_e('Text Color', 'fluxstack'); ?></label>
                <input type="color" class="fluxstack-field__color" name="fluxstack_site_settings[header_topbar_color]" value="<?php echo esc_attr($settings['header_topbar_color'] ?? '#e2e8f0'); ?>">
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-checkbox"><input type="checkbox" name="fluxstack_site_settings[header_topbar_social]" value="1" <?php checked(!empty($settings['header_topbar_social'])); ?>> <?php esc_html_e('Show social icons in top bar', 'fluxstack'); ?></label>
            </div>
        </div>
    </section>

    <section class="fluxstack-section">
        <h2 class="fluxstack-section__title"><?php esc_html_e('CTA Button', 'fluxstack'); ?></h2>
        <p class="fluxstack-section__desc"><?php esc_html_e('An optional call-to-action button displayed alongside the main navigation.', 'fluxstack'); ?></p>
        <div class="fluxstack-settings-form">
            <div class="fluxstack-field">
                <label class="fluxstack-field__label"><?php esc_html_e('Button Text', 'fluxstack'); ?></label>
                <input type="text" class="fluxstack-field__input" name="fluxstack_site_settings[header_cta_text]" value="<?php echo esc_attr($settings['header_cta_text'] ?? ''); ?>" placeholder="<?php esc_attr_e('Get a Quote', 'fluxstack'); ?>">
                <span class="fluxstack-field__help"><?php esc_html_e('Leave empty to hide the button entirely.', 'fluxstack'); ?></span>
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-field__label"><?php esc_html_e('Button URL', 'fluxstack'); ?></label>
                <input type="text" class="fluxstack-field__input" name="fluxstack_site_settings[header_cta_url]" value="<?php echo esc_attr($settings['header_cta_url'] ?? ''); ?>" placeholder="/contact">
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-field__label"><?php esc_html_e('Button Style', 'fluxstack'); ?></label>
                <select class="fluxstack-field__select" name="fluxstack_site_settings[header_cta_style]">
                    <option value="filled" <?php selected($settings['header_cta_style'] ?? '', 'filled'); ?>>Filled (solid background)</option>
                    <option value="outline" <?php selected($settings['header_cta_style'] ?? '', 'outline'); ?>>Outline (border only)</option>
                </select>
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-checkbox"><input type="checkbox" name="fluxstack_site_settings[header_cta_new_tab]" value="1" <?php checked(!empty($settings['header_cta_new_tab'])); ?>> <?php esc_html_e('Open in new tab', 'fluxstack'); ?></label>
            </div>
        </div>
    </section>

    <section class="fluxstack-section">
        <h2 class="fluxstack-section__title"><?php esc_html_e('Mobile Menu', 'fluxstack'); ?></h2>
        <p class="fluxstack-section__desc"><?php esc_html_e('Settings for the mobile/hamburger navigation menu.', 'fluxstack'); ?></p>
        <div class="fluxstack-settings-form">
            <div class="fluxstack-field">
                <label class="fluxstack-field__label"><?php esc_html_e('Mobile Menu Style', 'fluxstack'); ?></label>
                <select class="fluxstack-field__select" name="fluxstack_site_settings[header_mobile_style]">
                    <option value="slide-right" <?php selected($settings['header_mobile_style'] ?? '', 'slide-right'); ?>>Slide from right</option>
                    <option value="slide-left" <?php selected($settings['header_mobile_style'] ?? '', 'slide-left'); ?>>Slide from left</option>
                    <option value="fullscreen" <?php selected($settings['header_mobile_style'] ?? '', 'fullscreen'); ?>>Full screen overlay</option>
                    <option value="dropdown" <?php selected($settings['header_mobile_style'] ?? '', 'dropdown'); ?>>Dropdown below header</option>
                </select>
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-checkbox"><input type="checkbox" name="fluxstack_site_settings[header_mobile_cta]" value="1" <?php checked(!empty($settings['header_mobile_cta'])); ?>> <?php esc_html_e('Show CTA button in mobile menu', 'fluxstack'); ?></label>
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-checkbox"><input type="checkbox" name="fluxstack_site_settings[header_mobile_social]" value="1" <?php checked(!empty($settings['header_mobile_social'])); ?>> <?php esc_html_e('Show social icons in mobile menu', 'fluxstack'); ?></label>
            </div>
        </div>
    </section>
</div>
