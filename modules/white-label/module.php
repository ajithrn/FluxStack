<?php

use App\Modules\BaseModule;

return new class extends BaseModule
{
    public function id(): string { return 'white-label'; }
    public function name(): string { return 'White Label'; }
    public function description(): string { return 'Admin branding, login page, color scheme, and dashboard cleanup.'; }
    public function category(): string { return 'feature'; }
    public function enabledByDefault(): bool { return true; }

    public function register(): void
    {
        $s = $this->getSettings();

        // Login page
        add_filter('login_headerurl', fn() => $s['agency_url'] ?: home_url('/'));
        add_filter('login_headertext', fn() => $s['agency_name'] ?: get_bloginfo('name'));
        add_action('login_enqueue_scripts', [$this, 'loginStyles']);

        // Admin footer
        add_filter('admin_footer_text', [$this, 'adminFooterLeft'], 9999);
        add_filter('update_footer', [$this, 'adminFooterRight'], 9999);

        // Cleanup
        remove_action('wp_head', 'wp_generator');
        add_action('init', fn() => remove_theme_support('core-block-patterns'));
        add_action('wp_dashboard_setup', [$this, 'cleanDashboard']);

        // Admin color scheme
        add_action('admin_enqueue_scripts', [$this, 'adminStyles']);

        // Hide unwanted editor buttons/panels
        add_action('admin_enqueue_scripts', [$this, 'editorCleanup']);

        // Register settings tab on Modules page
        add_action('fluxstack_modules_tabs', [$this, 'registerTab']);
        add_action('fluxstack_modules_panels', [$this, 'registerPanel']);
    }

    private function getSettings(): array
    {
        $site = get_option('fluxstack_site_settings', []);
        return [
            'agency_name' => $site['developer_name'] ?? 'Developer',
            'agency_url' => $site['developer_url'] ?? '',
            'platform_name' => $site['platform_name'] ?? 'FluxStack',
            'admin_primary' => $site['wl_admin_primary'] ?? '#1e1e1e',
            'admin_accent' => $site['wl_admin_accent'] ?? '#4f46e5',
            'hide_wp_logo' => $site['wl_hide_wp_logo'] ?? '',
            'hide_updates' => $site['wl_hide_updates'] ?? '',
            'hide_comments' => $site['wl_hide_comments'] ?? '',
            'custom_admin_css' => $site['wl_custom_css'] ?? '',
        ];
    }

    public function loginStyles(): void
    {
        $site = get_option('fluxstack_site_settings', []);
        $logo = $site['logo'] ?? '';
        $accent = $site['wl_admin_accent'] ?? '#4f46e5';
        ?>
        <style>
            body.login { background: #f1f5f9; }
            .login h1 a {
                <?php if ($logo) : ?>background-image: url('<?php echo esc_url($logo); ?>');<?php endif; ?>
                background-size: contain; background-position: center;
                width: 100%; height: 84px; background-repeat: no-repeat;
            }
            .login form { border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
            .login .button-primary { background: <?php echo esc_attr($accent); ?>; border-color: <?php echo esc_attr($accent); ?>; border-radius: 6px; }
            .login .button-primary:hover { background: <?php echo esc_attr($accent); ?>; opacity: 0.9; }
            .login #nav a, .login #backtoblog a { color: <?php echo esc_attr($accent); ?>; }
            #login { padding-top: 6%; }
        </style>
        <?php
    }

    public function adminFooterLeft(): string
    {
        $s = $this->getSettings();
        return sprintf(
            'Built with <strong>%s</strong> by <a href="%s" target="_blank">%s</a>',
            esc_html($s['platform_name']),
            esc_url($s['agency_url']),
            esc_html($s['agency_name'])
        );
    }

    public function adminFooterRight(): string
    {
        return date('F j, Y');
    }

    public function cleanDashboard(): void
    {
        remove_meta_box('dashboard_primary', 'dashboard', 'side');
        remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
        remove_meta_box('dashboard_site_health', 'dashboard', 'normal');

        $s = $this->getSettings();
        if ($s['hide_comments']) {
            remove_menu_page('edit-comments.php');
        }
    }

    public function adminStyles(): void
    {
        $s = $this->getSettings();
        $primary = $s['admin_primary'];
        $accent = $s['admin_accent'];
        ?>
        <style>
            :root { --fs-wl-primary: <?php echo esc_attr($primary); ?>; --fs-wl-accent: <?php echo esc_attr($accent); ?>; }
            #adminmenu, #adminmenu .wp-submenu, #adminmenuback, #adminmenuwrap { background: var(--fs-wl-primary); }
            #adminmenu a { color: rgba(255,255,255,0.7); }
            #adminmenu a:hover, #adminmenu li.menu-top:hover > a { color: #fff; }
            #adminmenu li.current a.menu-top, #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu { background: var(--fs-wl-accent); color: #fff; }
            #adminmenu li.current div.wp-menu-image:before, #adminmenu li.wp-has-current-submenu div.wp-menu-image:before { color: #fff; }
            #adminmenu div.wp-menu-image:before { color: rgba(255,255,255,0.6); }
            #adminmenu li:hover div.wp-menu-image:before { color: #fff; }
            #wpadminbar { background: var(--fs-wl-primary); }
            .wp-core-ui .button-primary { background: var(--fs-wl-accent); border-color: var(--fs-wl-accent); }
            .wp-core-ui .button-primary:hover { opacity: 0.9; }
            <?php if ($s['hide_wp_logo']) : ?>#wp-admin-bar-wp-logo { display: none !important; }<?php endif; ?>
            <?php if ($s['hide_updates']) : ?>#wp-admin-bar-updates, .update-plugins { display: none !important; }<?php endif; ?>
            <?php if ($s['custom_admin_css']) echo $s['custom_admin_css']; ?>
        </style>
        <?php
    }

    public function editorCleanup(): void
    {
        if (! get_current_screen() || ! get_current_screen()->is_block_editor()) return;
        echo '<style>
            #ast-block-templates-button-wrap,
            .kadence-toolbar-design-library,
            .components-button[aria-label="Spectra Page Settings"],
            .components-button[aria-label="Stackable Settings"] { display: none !important; }
        </style>';
    }

    public function registerTab(): void
    {
        echo '<button class="fluxstack-tabs__tab" data-tab="white-label"><span class="dashicons dashicons-admin-customizer"></span> ' . esc_html__('White Label', 'fluxstack') . '</button>';
    }

    public function registerPanel(): void
    {
        $site = get_option('fluxstack_site_settings', []);
        ?>
        <div class="fluxstack-panel" data-panel="white-label">
            <section class="fluxstack-section">
                <h2 class="fluxstack-section__title"><?php esc_html_e('Agency / Developer', 'fluxstack'); ?></h2>
                <p class="fluxstack-section__desc"><?php esc_html_e('Branding shown in admin footer and login page.', 'fluxstack'); ?></p>
                <div class="fluxstack-settings-form">
                    <div class="fluxstack-field">
                        <label class="fluxstack-field__label"><?php esc_html_e('Agency / Developer Name', 'fluxstack'); ?></label>
                        <input type="text" class="fluxstack-field__input" name="fluxstack_site_settings[developer_name]" value="<?php echo esc_attr($site['developer_name'] ?? ''); ?>" placeholder="Your Agency">
                    </div>
                    <div class="fluxstack-field">
                        <label class="fluxstack-field__label"><?php esc_html_e('Agency URL', 'fluxstack'); ?></label>
                        <div class="fluxstack-url-wrap">
                            <span class="fluxstack-url-wrap__prefix">https://</span>
                            <input type="text" class="fluxstack-url-wrap__input" name="fluxstack_site_settings[developer_url]" value="<?php echo esc_attr(str_replace(['https://', 'http://'], '', $site['developer_url'] ?? '')); ?>" placeholder="youragency.com" data-url-field>
                        </div>
                    </div>
                    <div class="fluxstack-field">
                        <label class="fluxstack-field__label"><?php esc_html_e('Platform Name', 'fluxstack'); ?></label>
                        <input type="text" class="fluxstack-field__input" name="fluxstack_site_settings[platform_name]" value="<?php echo esc_attr($site['platform_name'] ?? 'FluxStack'); ?>" placeholder="FluxStack">
                        <span class="fluxstack-field__help"><?php esc_html_e('Shown in footer: "Built with [Platform] by [Agency]"', 'fluxstack'); ?></span>
                    </div>
                </div>
            </section>

            <section class="fluxstack-section">
                <h2 class="fluxstack-section__title"><?php esc_html_e('Admin Color Scheme', 'fluxstack'); ?></h2>
                <p class="fluxstack-section__desc"><?php esc_html_e('Customize the WordPress admin sidebar and accent colors.', 'fluxstack'); ?></p>
                <div class="fluxstack-settings-form">
                    <div class="fluxstack-field">
                        <label class="fluxstack-field__label"><?php esc_html_e('Sidebar Background', 'fluxstack'); ?></label>
                        <input type="color" class="fluxstack-field__color" name="fluxstack_site_settings[wl_admin_primary]" value="<?php echo esc_attr($site['wl_admin_primary'] ?? '#1e1e1e'); ?>">
                    </div>
                    <div class="fluxstack-field">
                        <label class="fluxstack-field__label"><?php esc_html_e('Accent Color', 'fluxstack'); ?></label>
                        <input type="color" class="fluxstack-field__color" name="fluxstack_site_settings[wl_admin_accent]" value="<?php echo esc_attr($site['wl_admin_accent'] ?? '#4f46e5'); ?>">
                        <span class="fluxstack-field__help"><?php esc_html_e('Active menu items, buttons, and links.', 'fluxstack'); ?></span>
                    </div>
                </div>
            </section>

            <section class="fluxstack-section">
                <h2 class="fluxstack-section__title"><?php esc_html_e('Cleanup & Visibility', 'fluxstack'); ?></h2>
                <p class="fluxstack-section__desc"><?php esc_html_e('Hide unnecessary WordPress UI elements from clients.', 'fluxstack'); ?></p>
                <div class="fluxstack-settings-form">
                    <div class="fluxstack-field">
                        <label class="fluxstack-checkbox">
                            <input type="checkbox" name="fluxstack_site_settings[wl_hide_wp_logo]" value="1" <?php checked(!empty($site['wl_hide_wp_logo'])); ?>>
                            <?php esc_html_e('Hide WordPress logo from admin bar', 'fluxstack'); ?>
                        </label>
                    </div>
                    <div class="fluxstack-field">
                        <label class="fluxstack-checkbox">
                            <input type="checkbox" name="fluxstack_site_settings[wl_hide_updates]" value="1" <?php checked(!empty($site['wl_hide_updates'])); ?>>
                            <?php esc_html_e('Hide update notifications', 'fluxstack'); ?>
                        </label>
                    </div>
                    <div class="fluxstack-field">
                        <label class="fluxstack-checkbox">
                            <input type="checkbox" name="fluxstack_site_settings[wl_hide_comments]" value="1" <?php checked(!empty($site['wl_hide_comments'])); ?>>
                            <?php esc_html_e('Hide Comments menu', 'fluxstack'); ?>
                        </label>
                    </div>
                </div>
            </section>

            <section class="fluxstack-section">
                <h2 class="fluxstack-section__title"><?php esc_html_e('Custom CSS', 'fluxstack'); ?></h2>
                <p class="fluxstack-section__desc"><?php esc_html_e('Additional CSS injected into the admin and login pages.', 'fluxstack'); ?></p>
                <div class="fluxstack-settings-form">
                    <div class="fluxstack-field fluxstack-field--wide">
                        <label class="fluxstack-field__label"><?php esc_html_e('Admin Custom CSS', 'fluxstack'); ?></label>
                        <textarea class="fluxstack-field__textarea" name="fluxstack_site_settings[wl_custom_css]" rows="6" placeholder="/* Custom admin styles */"><?php echo esc_textarea($site['wl_custom_css'] ?? ''); ?></textarea>
                    </div>
                </div>
            </section>
        </div>
        <?php
    }
};
