<?php

use App\Modules\BaseModule;

return new class extends BaseModule
{
    public function id(): string { return 'module-manager'; }
    public function name(): string { return 'Module Manager'; }
    public function description(): string { return 'Admin page for enabling/disabling theme modules and blocks.'; }
    public function category(): string { return 'utility'; }
    public function enabledByDefault(): bool { return true; }

    public function register(): void
    {
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('wp_ajax_fluxstack_save_modules', [$this, 'ajaxSave']);
    }

    public function addAdminMenu(): void
    {
        add_theme_page(
            __('FluxStack Modules', 'fluxstack'),
            __('FluxStack', 'fluxstack'),
            'manage_options',
            'fluxstack-modules',
            [$this, 'renderPage']
        );
    }

    public function registerSettings(): void
    {
        register_setting('fluxstack_settings', 'fluxstack_modules');
    }

    public function ajaxSave(): void
    {
        check_ajax_referer('fluxstack_nonce', 'nonce');
        if (! current_user_can('manage_options')) wp_send_json_error();

        // Save module toggles
        $modules = isset($_POST['modules']) ? (array) $_POST['modules'] : [];
        $sanitized = [];
        $manager = $this->getManager();
        if ($manager) {
            foreach ($manager->getAll() as $id => $module) {
                $sanitized[$id] = ! empty($modules[$id]);
            }
        }

        // Also save any extra toggle IDs (site-settings sub-pages, etc.)
        foreach ($modules as $id => $value) {
            if (! isset($sanitized[$id])) {
                $sanitized[$id] = ! empty($value);
            }
        }

        $sanitized['module-manager'] = true;
        $sanitized['site-settings'] = true;
        update_option('fluxstack_modules', $sanitized);

        // Also save any site settings fields present (from module panels like white-label)
        if (isset($_POST['settings']) && is_array($_POST['settings'])) {
            $existing = get_option('fluxstack_site_settings', []);
            $incoming = (array) $_POST['settings'];
            $multiline = ['address', 'footer_text', 'head_scripts', 'body_scripts', 'wl_custom_css'];

            foreach ($incoming as $key => $value) {
                $existing[$key] = in_array($key, $multiline) ? wp_kses_post($value) : sanitize_text_field($value);
            }
            update_option('fluxstack_site_settings', $existing);
        }

        wp_send_json_success();
    }

    public function renderPage(): void
    {
        $manager = $this->getManager();
        if (! $manager) { echo '<p>Module system not loaded.</p>'; return; }

        $coreModules = ['module-manager', 'site-settings'];
        $blocks = $manager->getBlocks();
        $nonBlockModules = $manager->getNonBlockModules();

        // Separate site-settings sub-pages from regular modules
        $siteSettingsModule = $manager->get('site-settings');
        $subPages = $siteSettingsModule ? $siteSettingsModule->getSubPages() : [];

        $grouped = [];
        foreach ($nonBlockModules as $module) {
            $cat = $module->category();
            if (! isset($grouped[$cat])) $grouped[$cat] = [];
            $grouped[$cat][] = $module;
        }

        $nonce = wp_create_nonce('fluxstack_nonce');
        include __DIR__ . '/views/modules-page.php';
    }

    public function enqueueAssets(string $hook): void
    {
        if ($hook !== 'appearance_page_fluxstack-modules') return;

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

    private function getManager()
    {
        if (function_exists('app') && app()->bound(\App\Modules\ModuleManager::class)) {
            return app(\App\Modules\ModuleManager::class);
        }
        return $GLOBALS['fluxstack_manager'] ?? null;
    }
};
