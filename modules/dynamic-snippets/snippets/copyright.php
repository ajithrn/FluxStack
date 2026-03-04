<?php
/**
 * Copyright snippet
 * Renders copyright text with dynamic year and site name.
 */
return array(
    'name'     => 'copyright',
    'label'    => __('Copyright', 'fluxstack'),
    'category' => 'fluxstack',
    'render'   => function() {
        $copyright_text = FS_Utils::get_theme_option('copyright_text', '© {year} {site_name}. All rights reserved.');
        $site_name = get_bloginfo('name');
        $year = date('Y');
        
        $replacements = array(
            '{year}'      => $year,
            '{site_name}' => $site_name,
            '[year]'      => $year,
            '[site_name]' => $site_name,
        );
        
        return wp_kses_post(str_replace(
            array_keys($replacements),
            array_values($replacements),
            $copyright_text
        ));
    },
);
