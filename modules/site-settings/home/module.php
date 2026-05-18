<?php
return [
    'id' => 'site-settings-home',
    'title' => 'Home Page',
    'slug' => 'fluxstack-site-home',
    'priority' => 10,
    'default' => false,
    'description' => 'Home page hero, featured sections, and layout options.',
    'callback' => function () {
        $settings = get_option('fluxstack_site_settings', []);
        include __DIR__ . '/views/page.php';
    },
];
