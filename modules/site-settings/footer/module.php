<?php
return [
    'id' => 'site-settings-footer',
    'title' => 'Footer',
    'slug' => 'fluxstack-site-footer',
    'priority' => 30,
    'default' => false,
    'description' => 'Footer layout, columns, content, and bottom bar.',
    'callback' => function () {
        $settings = get_option('fluxstack_site_settings', []);
        include __DIR__ . '/views/page.php';
    },
];
