<div class="fluxstack-app" id="fluxstack-site-settings">
    <div class="fluxstack-header">
        <div class="fluxstack-header__left">
            <h1 class="fluxstack-header__title">Site Settings</h1>
        </div>
        <div class="fluxstack-header__right">
            <button type="button" class="fluxstack-btn fluxstack-btn--primary" id="fluxstack-save" data-action="fluxstack_save_site_settings">
                <span class="fluxstack-btn__text"><?php esc_html_e('Save Changes', 'fluxstack'); ?></span>
                <span class="dashicons dashicons-saved"></span>
            </button>
        </div>
    </div>
    <div class="fluxstack-toast" id="fluxstack-toast" hidden></div>

    <nav class="fluxstack-tabs">
        <button class="fluxstack-tabs__tab is-active" data-tab="branding"><span class="dashicons dashicons-format-image"></span> <?php esc_html_e('Branding', 'fluxstack'); ?></button>
        <button class="fluxstack-tabs__tab" data-tab="contact"><span class="dashicons dashicons-phone"></span> <?php esc_html_e('Contact', 'fluxstack'); ?></button>
        <button class="fluxstack-tabs__tab" data-tab="social"><span class="dashicons dashicons-share"></span> <?php esc_html_e('Social', 'fluxstack'); ?></button>
        <button class="fluxstack-tabs__tab" data-tab="analytics"><span class="dashicons dashicons-chart-bar"></span> <?php esc_html_e('Analytics', 'fluxstack'); ?></button>
        <?php do_action('fluxstack_site_settings_tabs'); ?>
    </nav>

    <!-- Branding -->
    <div class="fluxstack-panel is-active" data-panel="branding">
        <section class="fluxstack-section">
            <h2 class="fluxstack-section__title"><?php esc_html_e('Logos', 'fluxstack'); ?></h2>
            <p class="fluxstack-section__desc"><?php esc_html_e('Upload site logos. These are used in the header, footer, and login page.', 'fluxstack'); ?></p>
            <div class="fluxstack-settings-form">
                <div class="fluxstack-field">
                    <label class="fluxstack-field__label"><?php esc_html_e('Primary Logo', 'fluxstack'); ?></label>
                    <div class="fluxstack-upload" data-field="logo">
                        <div class="fluxstack-upload__preview">
                            <?php if (!empty($settings['logo'])) : ?>
                                <img src="<?php echo esc_url($settings['logo']); ?>" alt="">
                            <?php else : ?>
                                <span class="dashicons dashicons-format-image"></span>
                            <?php endif; ?>
                        </div>
                        <div class="fluxstack-upload__actions">
                            <button type="button" class="fluxstack-upload__btn js-upload-btn"><?php esc_html_e('Upload', 'fluxstack'); ?></button>
                            <?php if (!empty($settings['logo'])) : ?>
                                <button type="button" class="fluxstack-upload__btn fluxstack-upload__btn--remove js-remove-btn"><?php esc_html_e('Remove', 'fluxstack'); ?></button>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="fluxstack_site_settings[logo]" value="<?php echo esc_attr($settings['logo'] ?? ''); ?>">
                    </div>
                    <span class="fluxstack-field__help"><?php esc_html_e('Used in header. SVG or PNG recommended.', 'fluxstack'); ?></span>
                </div>
                <div class="fluxstack-field">
                    <label class="fluxstack-field__label"><?php esc_html_e('Footer Logo', 'fluxstack'); ?></label>
                    <div class="fluxstack-upload" data-field="logo_footer">
                        <div class="fluxstack-upload__preview">
                            <?php if (!empty($settings['logo_footer'])) : ?>
                                <img src="<?php echo esc_url($settings['logo_footer']); ?>" alt="">
                            <?php else : ?>
                                <span class="dashicons dashicons-format-image"></span>
                            <?php endif; ?>
                        </div>
                        <div class="fluxstack-upload__actions">
                            <button type="button" class="fluxstack-upload__btn js-upload-btn"><?php esc_html_e('Upload', 'fluxstack'); ?></button>
                            <?php if (!empty($settings['logo_footer'])) : ?>
                                <button type="button" class="fluxstack-upload__btn fluxstack-upload__btn--remove js-remove-btn"><?php esc_html_e('Remove', 'fluxstack'); ?></button>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="fluxstack_site_settings[logo_footer]" value="<?php echo esc_attr($settings['logo_footer'] ?? ''); ?>">
                    </div>
                    <span class="fluxstack-field__help"><?php esc_html_e('Optional light version for dark footer backgrounds.', 'fluxstack'); ?></span>
                </div>
                <div class="fluxstack-field">
                    <label class="fluxstack-field__label"><?php esc_html_e('Favicon', 'fluxstack'); ?></label>
                    <div class="fluxstack-upload" data-field="favicon">
                        <div class="fluxstack-upload__preview" style="width:60px;height:60px;">
                            <?php if (!empty($settings['favicon'])) : ?>
                                <img src="<?php echo esc_url($settings['favicon']); ?>" alt="">
                            <?php else : ?>
                                <span class="dashicons dashicons-format-image"></span>
                            <?php endif; ?>
                        </div>
                        <div class="fluxstack-upload__actions">
                            <button type="button" class="fluxstack-upload__btn js-upload-btn"><?php esc_html_e('Upload', 'fluxstack'); ?></button>
                            <?php if (!empty($settings['favicon'])) : ?>
                                <button type="button" class="fluxstack-upload__btn fluxstack-upload__btn--remove js-remove-btn"><?php esc_html_e('Remove', 'fluxstack'); ?></button>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="fluxstack_site_settings[favicon]" value="<?php echo esc_attr($settings['favicon'] ?? ''); ?>">
                    </div>
                </div>
            </div>
        </section>
        <section class="fluxstack-section">
            <h2 class="fluxstack-section__title"><?php esc_html_e('Footer', 'fluxstack'); ?></h2>
            <div class="fluxstack-settings-form">
                <div class="fluxstack-field fluxstack-field--wide">
                    <label class="fluxstack-field__label"><?php esc_html_e('Copyright Text', 'fluxstack'); ?></label>
                    <input type="text" class="fluxstack-field__input" name="fluxstack_site_settings[copyright]" value="<?php echo esc_attr($settings['copyright'] ?? ''); ?>" placeholder="&copy; {year} Company Name. All rights reserved.">
                    <span class="fluxstack-field__help"><?php esc_html_e('Use {year} for dynamic year replacement.', 'fluxstack'); ?></span>
                </div>
                <div class="fluxstack-field fluxstack-field--wide">
                    <label class="fluxstack-field__label"><?php esc_html_e('Footer Text / Disclaimer', 'fluxstack'); ?></label>
                    <textarea class="fluxstack-field__textarea" name="fluxstack_site_settings[footer_text]" rows="3" placeholder="<?php esc_attr_e('Optional footer disclaimer or additional text...', 'fluxstack'); ?>"><?php echo esc_textarea($settings['footer_text'] ?? ''); ?></textarea>
                </div>
            </div>
        </section>
    </div>

    <!-- Contact -->
    <div class="fluxstack-panel" data-panel="contact">
        <section class="fluxstack-section">
            <h2 class="fluxstack-section__title"><?php esc_html_e('Contact Information', 'fluxstack'); ?></h2>
            <p class="fluxstack-section__desc"><?php esc_html_e('Used in header, footer, contact pages, and structured data (schema).', 'fluxstack'); ?></p>
            <div class="fluxstack-settings-form">
                <div class="fluxstack-field">
                    <label class="fluxstack-field__label"><?php esc_html_e('Phone', 'fluxstack'); ?></label>
                    <input type="text" class="fluxstack-field__input" name="fluxstack_site_settings[phone]" value="<?php echo esc_attr($settings['phone'] ?? ''); ?>" placeholder="+1 (555) 000-0000">
                </div>
                <div class="fluxstack-field">
                    <label class="fluxstack-field__label"><?php esc_html_e('Email', 'fluxstack'); ?></label>
                    <input type="email" class="fluxstack-field__input" name="fluxstack_site_settings[email]" value="<?php echo esc_attr($settings['email'] ?? ''); ?>" placeholder="hello@example.com">
                </div>
                <div class="fluxstack-field">
                    <label class="fluxstack-field__label"><?php esc_html_e('Secondary Phone', 'fluxstack'); ?></label>
                    <input type="text" class="fluxstack-field__input" name="fluxstack_site_settings[phone_2]" value="<?php echo esc_attr($settings['phone_2'] ?? ''); ?>" placeholder="Optional">
                </div>
                <div class="fluxstack-field">
                    <label class="fluxstack-field__label"><?php esc_html_e('Business Hours', 'fluxstack'); ?></label>
                    <input type="text" class="fluxstack-field__input" name="fluxstack_site_settings[hours]" value="<?php echo esc_attr($settings['hours'] ?? ''); ?>" placeholder="Mon-Fri: 9am - 5pm">
                </div>
                <div class="fluxstack-field fluxstack-field--wide">
                    <label class="fluxstack-field__label"><?php esc_html_e('Address', 'fluxstack'); ?></label>
                    <textarea class="fluxstack-field__textarea" name="fluxstack_site_settings[address]" rows="3" placeholder="123 Main Street&#10;City, State 12345&#10;Country"><?php echo esc_textarea($settings['address'] ?? ''); ?></textarea>
                </div>
                <div class="fluxstack-field fluxstack-field--wide">
                    <label class="fluxstack-field__label"><?php esc_html_e('Google Maps Embed URL', 'fluxstack'); ?></label>
                    <input type="url" class="fluxstack-field__input" name="fluxstack_site_settings[map_url]" value="<?php echo esc_attr($settings['map_url'] ?? ''); ?>" placeholder="https://www.google.com/maps/embed?pb=...">
                    <span class="fluxstack-field__help"><?php esc_html_e('Paste the iframe src URL from Google Maps embed code.', 'fluxstack'); ?></span>
                </div>
            </div>
        </section>
    </div>

    <!-- Social -->
    <div class="fluxstack-panel" data-panel="social">
        <section class="fluxstack-section">
            <h2 class="fluxstack-section__title"><?php esc_html_e('Social Profiles', 'fluxstack'); ?></h2>
            <p class="fluxstack-section__desc"><?php esc_html_e('Social media links displayed in header/footer and used for schema markup.', 'fluxstack'); ?></p>
            <div class="fluxstack-settings-form">
                <?php
                $socials = [
                    'facebook' => ['Facebook', 'facebook.com/yourpage'],
                    'instagram' => ['Instagram', 'instagram.com/yourhandle'],
                    'twitter' => ['X (Twitter)', 'x.com/yourhandle'],
                    'linkedin' => ['LinkedIn', 'linkedin.com/company/...'],
                    'youtube' => ['YouTube', 'youtube.com/@channel'],
                    'tiktok' => ['TikTok', 'tiktok.com/@handle'],
                    'github' => ['GitHub', 'github.com/username'],
                    'pinterest' => ['Pinterest', 'pinterest.com/username'],
                ];
                foreach ($socials as $key => [$label, $placeholder]) :
                    $val = str_replace(['https://', 'http://'], '', $settings['social_' . $key] ?? '');
                ?>
                <div class="fluxstack-field">
                    <label class="fluxstack-field__label"><?php echo esc_html($label); ?></label>
                    <div class="fluxstack-url-wrap">
                        <span class="fluxstack-url-wrap__prefix">https://</span>
                        <input type="text" class="fluxstack-url-wrap__input" name="fluxstack_site_settings[social_<?php echo $key; ?>]" value="<?php echo esc_attr($val); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" data-url-field>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <!-- Analytics -->
    <div class="fluxstack-panel" data-panel="analytics">
        <section class="fluxstack-section">
            <h2 class="fluxstack-section__title"><?php esc_html_e('Tracking & Analytics', 'fluxstack'); ?></h2>
            <p class="fluxstack-section__desc"><?php esc_html_e('Analytics IDs and tracking scripts injected into the site.', 'fluxstack'); ?></p>
            <div class="fluxstack-settings-form">
                <div class="fluxstack-field">
                    <label class="fluxstack-field__label"><?php esc_html_e('Google Tag Manager ID', 'fluxstack'); ?></label>
                    <input type="text" class="fluxstack-field__input" name="fluxstack_site_settings[gtm_id]" value="<?php echo esc_attr($settings['gtm_id'] ?? ''); ?>" placeholder="GTM-XXXXXXX">
                </div>
                <div class="fluxstack-field">
                    <label class="fluxstack-field__label"><?php esc_html_e('GA4 Measurement ID', 'fluxstack'); ?></label>
                    <input type="text" class="fluxstack-field__input" name="fluxstack_site_settings[ga4_id]" value="<?php echo esc_attr($settings['ga4_id'] ?? ''); ?>" placeholder="G-XXXXXXXXXX">
                </div>
                <div class="fluxstack-field fluxstack-field--wide">
                    <label class="fluxstack-field__label"><?php esc_html_e('Head Scripts', 'fluxstack'); ?></label>
                    <textarea class="fluxstack-field__textarea" name="fluxstack_site_settings[head_scripts]" rows="5" placeholder="<!-- Tracking pixels, verification tags, etc. -->"><?php echo esc_textarea($settings['head_scripts'] ?? ''); ?></textarea>
                    <span class="fluxstack-field__help"><?php esc_html_e('Injected before </head>. Use for tracking pixels, meta verification, etc.', 'fluxstack'); ?></span>
                </div>
                <div class="fluxstack-field fluxstack-field--wide">
                    <label class="fluxstack-field__label"><?php esc_html_e('Body Scripts (after open)', 'fluxstack'); ?></label>
                    <textarea class="fluxstack-field__textarea" name="fluxstack_site_settings[body_scripts]" rows="4" placeholder="<!-- GTM noscript, etc. -->"><?php echo esc_textarea($settings['body_scripts'] ?? ''); ?></textarea>
                    <span class="fluxstack-field__help"><?php esc_html_e('Injected after <body>. Typically used for GTM noscript fallback.', 'fluxstack'); ?></span>
                </div>
            </div>
        </section>
    </div>

    <!-- Module-registered settings panels -->
    <?php do_action('fluxstack_site_settings_panels', $settings); ?>
</div>
