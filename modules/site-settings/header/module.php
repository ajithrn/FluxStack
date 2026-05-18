<?php
return [
    'id' => 'site-settings-header',
    'title' => 'Header',
    'slug' => 'fluxstack-site-header',
    'priority' => 20,
    'default' => false,
    'description' => 'Header layout, navigation style, CTA button, and top bar.',
    'callback' => function () {
        $settings = get_option('fluxstack_site_settings', []);
        include __DIR__ . '/views/page.php';
    },
];
