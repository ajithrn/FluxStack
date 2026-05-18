<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Auto-discovered module directories
    |--------------------------------------------------------------------------
    | The module manager will scan these directories for module.php files
    */
    'paths' => [
        get_theme_file_path('modules'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Core modules (always enabled, cannot be disabled)
    |--------------------------------------------------------------------------
    */
    'core' => [
        'module-manager',
        'site-settings',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default enabled modules for new installations
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'theme-options',
        'white-label',
    ],
];
