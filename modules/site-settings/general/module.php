<?php
return [
    'id' => 'site-settings-general',
    'title' => 'General',
    'slug' => 'fluxstack-site-settings',
    'priority' => 1,
    'default' => true,
    'core' => true,
    'description' => 'Branding, contact info, social links, and analytics.',
    'callback' => function () {
        $settings = get_option('fluxstack_site_settings', []);
        include __DIR__ . '/views/page.php';
    },
];
