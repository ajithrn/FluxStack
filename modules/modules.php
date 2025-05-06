<?php
/**
 * Module Loader
 *
 * @package FluxStack
 */

// Load module manager first
require_once dirname( __FILE__ ) . '/module-manager/module-manager.php';

// Initialize module manager
FS_Module_Manager::init();

// Get module settings
$module_settings = FS_Module_Manager::get_module_settings();

// Conditionally load modules based on settings
if (FS_Module_Manager::can_load_module('bricks')) {
    require_once dirname( __FILE__ ) . '/bricks/bricks.php';
}

if (FS_Module_Manager::can_load_module('dynamic-snippets')) {
    require_once dirname( __FILE__ ) . '/dynamic-snippets/dynamic-snippets.php';
}

if (FS_Module_Manager::can_load_module('utility-functions')) {
    require_once dirname( __FILE__ ) . '/utility-functions/utility-functions.php';
}

if (FS_Module_Manager::can_load_module('theme-options')) {
    require_once dirname( __FILE__ ) . '/theme-options/theme-options.php';
}

if (FS_Module_Manager::can_load_module('testimonials')) {
    require_once dirname( __FILE__ ) . '/testimonials/testimonials.php';
}

if (FS_Module_Manager::can_load_module('image-gallery')) {
    require_once dirname( __FILE__ ) . '/image-gallery/image-gallery.php';
}

if (FS_Module_Manager::can_load_module('white-label')) {
    require_once dirname( __FILE__ ) . '/white-label/white-label.php';
}

if (FS_Module_Manager::can_load_module('teams')) {
    require_once dirname( __FILE__ ) . '/teams/teams.php';
}

if (FS_Module_Manager::can_load_module('publications')) {
    require_once dirname( __FILE__ ) . '/publications/publications.php';
}

if (FS_Module_Manager::can_load_module('news-archives')) {
    require_once dirname( __FILE__ ) . '/news-archives/news-archives.php';
}

if (FS_Module_Manager::can_load_module('portfolio')) {
    require_once dirname( __FILE__ ) . '/portfolio/portfolio.php';
}

// Initialize modules that are enabled
if (FS_Module_Manager::can_load_module('bricks')) {
    FS_Bricks::init();
}

if (FS_Module_Manager::can_load_module('utility-functions')) {
    FS_Utils::init();
}

if (FS_Module_Manager::can_load_module('theme-options')) {
    FS_Theme_Options::init();
}

if (FS_Module_Manager::can_load_module('dynamic-snippets')) {
    FS_Dynamic_Snippets::init();
}

if (FS_Module_Manager::can_load_module('image-gallery')) {
    FS_Image_Gallery::init();
}

if (FS_Module_Manager::can_load_module('testimonials')) {
    FS_Testimonials::init();
}

if (FS_Module_Manager::can_load_module('white-label')) {
    FS_White_Label::init();
}

if (FS_Module_Manager::can_load_module('teams')) {
    FS_Teams::init();
}

if (FS_Module_Manager::can_load_module('publications')) {
    FS_Publications::init();
}

if (FS_Module_Manager::can_load_module('news-archives')) {
    FS_News_Archives::init();
}

if (FS_Module_Manager::can_load_module('portfolio')) {
    FS_Portfolio::init();
}
