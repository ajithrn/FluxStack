<?php

use App\Modules\BaseModule;

return new class extends BaseModule
{
    public function id(): string { return 'site-settings'; }
    public function name(): string { return 'Site Settings'; }
    public function description(): string { return 'Global site settings with extensible sub-pages.'; }
    public function category(): string { return 'utility'; }
    public function enabledByDefault(): bool { return true; }

    private array $subPages = [];

    public function register(): void
    {
        // Discover immediately so sub-pages are available for admin_menu
        $this->discoverSubPages();

        add_action('admin_menu', [$this, 'addAdminMenus']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('wp_ajax_fluxstack_save_site_settings', [$this, 'ajaxSave']);
    }

    private function discoverSubPages(): void
    {
        $dir = __DIR__;
        $subDirs = glob($dir . '/*', GLOB_ONLYDIR);

        foreach ($subDirs as $subDir) {
            $moduleFile = $subDir . '/module.php';
            if (file_exists($moduleFile)) {
                $sub = require $moduleFile;
                if (is_array($sub) && ! empty($sub['id'])) {
                    $this->subPages[] = $sub;
                }
            }
        }

        // Sort by priority (lower = first)
        usort($this->subPages, function ($a, $b) {
            return ($a['priority'] ?? 50) - ($b['priority'] ?? 50);
        });
    }

    public function getSubPages(): array
    {
        return $this->subPages;
    }

    private function isSubPageEnabled(array $sub): bool
    {
        if (! empty($sub['core'])) return true;
        $settings = get_option('fluxstack_modules', []);
        return ! empty($settings[$sub['id']]);
    }

    public function addAdminMenus(): void
    {
        // Parent menu — renders General page
        add_menu_page(
            __('Site Settings', 'fluxstack'),
            __('Site Settings', 'fluxstack'),
            'manage_options',
            'fluxstack-site-settings',
            [$this, 'renderGeneralPage'],
            'dashicons-admin-site-alt3',
            3
        );

        // Rename the auto-created first submenu from "Site Settings" to "General"
        global $submenu;
        if (isset($submenu['fluxstack-site-settings'])) {
            $submenu['fluxstack-site-settings'][0][0] = __('General', 'fluxstack');
        }

        // Register enabled sub-pages (skip general since parent handles it)
        foreach ($this->subPages as $sub) {
            if (! empty($sub['core'])) continue; // General is handled by parent
            if (! $this->isSubPageEnabled($sub)) continue;

            add_submenu_page(
                'fluxstack-site-settings',
                $sub['title'],
                $sub['title'],
                'manage_options',
                $sub['slug'],
                $sub['callback']
            );
        }

        do_action('fluxstack_register_settings_pages');
    }

    public function renderGeneralPage(): void
    {
        $settings = get_option('fluxstack_site_settings', []);
        include __DIR__ . '/general/views/page.php';
    }

    public function enqueueAssets(string $hook): void
    {
        // Match our pages: toplevel or any sub-page
        if ($hook !== 'toplevel_page_fluxstack-site-settings'
            && strpos($hook, 'fluxstack-site') === false
            && strpos($hook, 'site-settings') === false) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style('fluxstack-admin', get_theme_file_uri('modules/module-manager/assets/admin.css'), [], '2.0.0');
        wp_enqueue_script('fluxstack-admin', get_theme_file_uri('modules/module-manager/assets/admin.js'), ['jquery'], '2.0.0', true);
        wp_localize_script('fluxstack-admin', 'fluxstackAdmin', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fluxstack_nonce'),
            'strings' => [
                'saving' => __('Saving...', 'fluxstack'),
                'saved' => __('Settings saved!', 'fluxstack'),
                'error' => __('Error saving.', 'fluxstack'),
            ],
        ]);
    }

    public function ajaxSave(): void
    {
        check_ajax_referer('fluxstack_nonce', 'nonce');
        if (! current_user_can('manage_options')) wp_send_json_error();

        $settings = isset($_POST['settings']) ? (array) $_POST['settings'] : [];
        $multiline = ['address', 'footer_text', 'footer_description', 'head_scripts', 'body_scripts', 'wl_custom_css', 'home_hero_subheading'];

        $existing = get_option('fluxstack_site_settings', []);
        foreach ($settings as $key => $value) {
            $existing[$key] = in_array($key, $multiline) ? wp_kses_post($value) : sanitize_text_field($value);
        }

        update_option('fluxstack_site_settings', $existing);
        wp_send_json_success();
    }

    public static function get(string $key, string $default = ''): string
    {
        $settings = get_option('fluxstack_site_settings', []);
        return $settings[$key] ?? $default;
    }
};
