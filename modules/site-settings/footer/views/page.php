<div class="fluxstack-app">
    <div class="fluxstack-header">
        <div class="fluxstack-header__left"><h1 class="fluxstack-header__title"><?php esc_html_e('Footer', 'fluxstack'); ?></h1></div>
        <div class="fluxstack-header__right">
            <button type="button" class="fluxstack-btn fluxstack-btn--primary" id="fluxstack-save" data-action="fluxstack_save_site_settings">
                <span class="fluxstack-btn__text"><?php esc_html_e('Save Changes', 'fluxstack'); ?></span>
                <span class="dashicons dashicons-saved"></span>
            </button>
        </div>
    </div>
    <div class="fluxstack-toast" id="fluxstack-toast" hidden></div>

    <section class="fluxstack-section">
        <h2 class="fluxstack-section__title"><?php esc_html_e('Layout', 'fluxstack'); ?></h2>
        <p class="fluxstack-section__desc"><?php esc_html_e('Control the footer structure, column count, and color scheme.', 'fluxstack'); ?></p>
        <div class="fluxstack-settings-form">
            <div class="fluxstack-field">
                <label class="fluxstack-field__label"><?php esc_html_e('Column Layout', 'fluxstack'); ?></label>
                <select class="fluxstack-field__select" name="fluxstack_site_settings[footer_columns]">
                    <option value="4" <?php selected($settings['footer_columns'] ?? '', '4'); ?>>4 Columns</option>
                    <option value="3" <?php selected($settings['footer_columns'] ?? '3', '3'); ?>>3 Columns</option>
                    <option value="2" <?php selected($settings['footer_columns'] ?? '', '2'); ?>>2 Columns</option>
                    <option value="1" <?php selected($settings['footer_columns'] ?? '', '1'); ?>>Single Column (Centered)</option>
                </select>
                <span class="fluxstack-field__help"><?php esc_html_e('Number of widget columns in the main footer area.', 'fluxstack'); ?></span>
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-field__label"><?php esc_html_e('Background', 'fluxstack'); ?></label>
                <input type="color" class="fluxstack-field__color" name="fluxstack_site_settings[footer_bg_color]" value="<?php echo esc_attr($settings['footer_bg_color'] ?? '#1e293b'); ?>">
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-field__label"><?php esc_html_e('Text Color', 'fluxstack'); ?></label>
                <input type="color" class="fluxstack-field__color" name="fluxstack_site_settings[footer_text_color]" value="<?php echo esc_attr($settings['footer_text_color'] ?? '#e2e8f0'); ?>">
            </div>
        </div>
    </section>

    <section class="fluxstack-section">
        <h2 class="fluxstack-section__title"><?php esc_html_e('Content', 'fluxstack'); ?></h2>
        <p class="fluxstack-section__desc"><?php esc_html_e('Footer text, description, and optional elements.', 'fluxstack'); ?></p>
        <div class="fluxstack-settings-form">
            <div class="fluxstack-field fluxstack-field--wide">
                <label class="fluxstack-field__label"><?php esc_html_e('Footer Description', 'fluxstack'); ?></label>
                <textarea class="fluxstack-field__textarea" name="fluxstack_site_settings[footer_description]" rows="3" placeholder="<?php esc_attr_e('A brief company description or tagline for the footer area...', 'fluxstack'); ?>"><?php echo esc_textarea($settings['footer_description'] ?? ''); ?></textarea>
                <span class="fluxstack-field__help"><?php esc_html_e('Typically displayed in the first footer column alongside the logo.', 'fluxstack'); ?></span>
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-checkbox"><input type="checkbox" name="fluxstack_site_settings[footer_show_social]" value="1" <?php checked(!empty($settings['footer_show_social'])); ?>> <?php esc_html_e('Show social media icons', 'fluxstack'); ?></label>
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-checkbox"><input type="checkbox" name="fluxstack_site_settings[footer_show_logo]" value="1" <?php checked(!empty($settings['footer_show_logo'])); ?>> <?php esc_html_e('Show footer logo', 'fluxstack'); ?></label>
            </div>
            <div class="fluxstack-field">
                <label class="fluxstack-checkbox"><input type="checkbox" name="fluxstack_site_settings[footer_show_newsletter]" value="1" <?php checked(!empty($settings['footer_show_newsletter'])); ?>> <?php esc_html_e('Show newsletter signup', 'fluxstack'); ?></label>
            </div>
        </div>
    </section>

    <section class="fluxstack-section">
        <h2 class="fluxstack-section__title"><?php esc_html_e('Bottom Bar', 'fluxstack'); ?></h2>
        <p class="fluxstack-section__desc"><?php esc_html_e('The thin strip at the very bottom with copyright and legal links.', 'fluxstack'); ?></p>
        <div class="fluxstack-settings-form">
            <div class="fluxstack-field fluxstack-field--wide">
                <label class="fluxstack-field__label"><?php esc_html_e('Copyright Text', 'fluxstack'); ?></label>
                <input type="text" class="fluxstack-field__input" name="fluxstack_site_settings[copyright]" value="<?php echo esc_attr($settings['copyright'] ?? ''); ?>" placeholder="&copy; {year} Company Name. All rights reserved.">
                <span class="fluxstack-field__help"><?php esc_html_e('{year} is replaced with the current year automatically.', 'fluxstack'); ?></span>
            </div>
            <div class="fluxstack-field fluxstack-field--wide">
                <label class="fluxstack-field__label"><?php esc_html_e('Footer Note / Disclaimer', 'fluxstack'); ?></label>
                <textarea class="fluxstack-field__textarea" name="fluxstack_site_settings[footer_text]" rows="2" placeholder="<?php esc_attr_e('Optional legal disclaimer or additional note...', 'fluxstack'); ?>"><?php echo esc_textarea($settings['footer_text'] ?? ''); ?></textarea>
            </div>
        </div>
    </section>
</div>
