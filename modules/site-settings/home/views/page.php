<div class="fluxstack-app">
    <div class="fluxstack-header">
        <div class="fluxstack-header__left"><h1 class="fluxstack-header__title"><?php esc_html_e('Home Page', 'fluxstack'); ?></h1></div>
        <div class="fluxstack-header__right">
            <button type="button" class="fluxstack-btn fluxstack-btn--primary" id="fluxstack-save" data-action="fluxstack_save_site_settings">
                <span class="fluxstack-btn__text"><?php esc_html_e('Save Changes', 'fluxstack'); ?></span>
                <span class="dashicons dashicons-saved"></span>
            </button>
        </div>
    </div>
    <div class="fluxstack-toast" id="fluxstack-toast" hidden></div>

    <section class="fluxstack-section">
        <h2 class="fluxstack-section__title"><?php esc_html_e('Hero Section', 'fluxstack'); ?></h2>
        <p class="fluxstack-section__desc"><?php esc_html_e('The main banner area visitors see first. Set the headline, supporting text, background, and call-to-action.', 'fluxstack'); ?></p>
        <div class="fluxstack-settings-form">
            <div class="fluxstack-field fluxstack-field--wide">
                <label class="fluxstack-field__label"><?php esc_html_e('Heading', 'fluxstack'); ?></label>
                <input type="text" class="fluxstack-field__input" name="fluxstack_site_settings[home_hero_heading]" value="<?php echo esc_attr($settings['home_hero_heading'] ?? ''); ?>" placeholder="<?php esc_attr_e('Your compelling headline here', 'fluxstack'); ?>">
                <span class="fluxstack-field__help"><?php esc_html_e('Keep it short and impactful — 6-10 words work best.', 'fluxstack'); ?></span>
            </div>
            <div class="fluxstack-field fluxstack-field--wide">
                <label class="fluxstack-field__label"><?php esc_html_e('Subheading', 'fluxstack'); ?></label>
                <textarea class="fluxstack-field__textarea" name="fluxstack_site_settings[home_hero_subheading]" rows="2" placeholder="<?php esc_attr_e('A brief supporting description that expands on the headline...', 'fluxstack'); ?>"><?php echo esc_textarea($settings['home_hero_subheading'] ?? ''); ?></textarea>
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-field__label"><?php esc_html_e('Background Image', 'fluxstack'); ?></label>
                <div class="fluxstack-upload" data-field="home_hero_image">
                    <div class="fluxstack-upload__preview">
                        <?php if (!empty($settings['home_hero_image'])) : ?><img src="<?php echo esc_url($settings['home_hero_image']); ?>" alt=""><?php else : ?><span class="dashicons dashicons-format-image"></span><?php endif; ?>
                    </div>
                    <div class="fluxstack-upload__actions">
                        <button type="button" class="fluxstack-upload__btn js-upload-btn"><?php esc_html_e('Upload', 'fluxstack'); ?></button>
                        <?php if (!empty($settings['home_hero_image'])) : ?><button type="button" class="fluxstack-upload__btn fluxstack-upload__btn--remove js-remove-btn"><?php esc_html_e('Remove', 'fluxstack'); ?></button><?php endif; ?>
                    </div>
                    <input type="hidden" name="fluxstack_site_settings[home_hero_image]" value="<?php echo esc_attr($settings['home_hero_image'] ?? ''); ?>">
                </div>
                <span class="fluxstack-field__help"><?php esc_html_e('Recommended: 1920×800px or larger. Dark images work best with white text.', 'fluxstack'); ?></span>
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-field__label"><?php esc_html_e('Primary Button Text', 'fluxstack'); ?></label>
                <input type="text" class="fluxstack-field__input" name="fluxstack_site_settings[home_hero_cta_text]" value="<?php echo esc_attr($settings['home_hero_cta_text'] ?? ''); ?>" placeholder="<?php esc_attr_e('Get Started', 'fluxstack'); ?>">
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-field__label"><?php esc_html_e('Primary Button URL', 'fluxstack'); ?></label>
                <input type="text" class="fluxstack-field__input" name="fluxstack_site_settings[home_hero_cta_url]" value="<?php echo esc_attr($settings['home_hero_cta_url'] ?? ''); ?>" placeholder="/contact">
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-field__label"><?php esc_html_e('Secondary Button Text', 'fluxstack'); ?></label>
                <input type="text" class="fluxstack-field__input" name="fluxstack_site_settings[home_hero_cta2_text]" value="<?php echo esc_attr($settings['home_hero_cta2_text'] ?? ''); ?>" placeholder="<?php esc_attr_e('Learn More', 'fluxstack'); ?>">
                <span class="fluxstack-field__help"><?php esc_html_e('Leave empty to show only one button.', 'fluxstack'); ?></span>
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-field__label"><?php esc_html_e('Secondary Button URL', 'fluxstack'); ?></label>
                <input type="text" class="fluxstack-field__input" name="fluxstack_site_settings[home_hero_cta2_url]" value="<?php echo esc_attr($settings['home_hero_cta2_url'] ?? ''); ?>" placeholder="/about">
            </div>
        </div>
    </section>

    <section class="fluxstack-section">
        <h2 class="fluxstack-section__title"><?php esc_html_e('Page Sections', 'fluxstack'); ?></h2>
        <p class="fluxstack-section__desc"><?php esc_html_e('Choose which content sections appear on the home page and in what order they display.', 'fluxstack'); ?></p>
        <div class="fluxstack-settings-form">
            <div class="fluxstack-field"><label class="fluxstack-checkbox"><input type="checkbox" name="fluxstack_site_settings[home_show_services]" value="1" <?php checked(!empty($settings['home_show_services'])); ?>> <?php esc_html_e('Services overview', 'fluxstack'); ?></label></div>
            <div class="fluxstack-field"><label class="fluxstack-checkbox"><input type="checkbox" name="fluxstack_site_settings[home_show_about]" value="1" <?php checked(!empty($settings['home_show_about'])); ?>> <?php esc_html_e('About / Introduction', 'fluxstack'); ?></label></div>
            <div class="fluxstack-field"><label class="fluxstack-checkbox"><input type="checkbox" name="fluxstack_site_settings[home_show_testimonials]" value="1" <?php checked(!empty($settings['home_show_testimonials'])); ?>> <?php esc_html_e('Testimonials', 'fluxstack'); ?></label></div>
            <div class="fluxstack-field"><label class="fluxstack-checkbox"><input type="checkbox" name="fluxstack_site_settings[home_show_team]" value="1" <?php checked(!empty($settings['home_show_team'])); ?>> <?php esc_html_e('Team members', 'fluxstack'); ?></label></div>
            <div class="fluxstack-field"><label class="fluxstack-checkbox"><input type="checkbox" name="fluxstack_site_settings[home_show_portfolio]" value="1" <?php checked(!empty($settings['home_show_portfolio'])); ?>> <?php esc_html_e('Portfolio / Projects', 'fluxstack'); ?></label></div>
            <div class="fluxstack-field"><label class="fluxstack-checkbox"><input type="checkbox" name="fluxstack_site_settings[home_show_blog]" value="1" <?php checked(!empty($settings['home_show_blog'])); ?>> <?php esc_html_e('Latest blog posts', 'fluxstack'); ?></label></div>
            <div class="fluxstack-field"><label class="fluxstack-checkbox"><input type="checkbox" name="fluxstack_site_settings[home_show_cta]" value="1" <?php checked(!empty($settings['home_show_cta'])); ?>> <?php esc_html_e('CTA banner', 'fluxstack'); ?></label></div>
        </div>
    </section>

    <?php do_action('fluxstack_home_settings', $settings); ?>
</div>
