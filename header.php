<?php
/**
 * Theme header — bridge for plugins that call get_header().
 * Renders the Blade layout opening via Acorn's view system.
 */

if (function_exists('app') && app()->bound('view')) {
    echo app('view')->make('sections.header-compat')->render();
} else {
    // Fallback if Acorn isn't available
    ?>
    <!doctype html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <div id="app">
    <?php
}
