<?php
/**
 * Module Loader
 *
 * Loads and initializes all FluxStack modules.
 * Core modules are loaded first, followed by content modules.
 *
 * @package FluxStack
 */

// Load module manager first
require_once dirname( __FILE__ ) . '/module-manager/module-manager.php';
FS_Module_Manager::init();

// Load base classes (required before content modules)
require_once dirname( __FILE__ ) . '/base/base-cpt-module.php';

/**
 * Core Modules
 * These provide foundational functionality for the theme.
 */

if (FS_Module_Manager::can_load_module('bricks')) {
    require_once dirname( __FILE__ ) . '/bricks/bricks.php';
    FS_Bricks::init();
}

if (FS_Module_Manager::can_load_module('utility-functions')) {
    require_once dirname( __FILE__ ) . '/utility-functions/utility-functions.php';
    FS_Utils::init();
}

if (FS_Module_Manager::can_load_module('theme-options')) {
    require_once dirname( __FILE__ ) . '/theme-options/theme-options.php';
    FS_Theme_Options::init();
}

if (FS_Module_Manager::can_load_module('dynamic-snippets')) {
    require_once dirname( __FILE__ ) . '/dynamic-snippets/dynamic-snippets.php';
    FS_Dynamic_Snippets::init();
}

if (FS_Module_Manager::can_load_module('seo')) {
    require_once dirname( __FILE__ ) . '/seo/seo.php';
    FS_SEO::init();
}

if (FS_Module_Manager::can_load_module('white-label')) {
    require_once dirname( __FILE__ ) . '/white-label/white-label.php';
    FS_White_Label::init();
}

/**
 * Content Modules (CPT)
 * These extend FS_Base_CPT_Module for content management.
 */

if (FS_Module_Manager::can_load_module('services')) {
    require_once dirname( __FILE__ ) . '/services/services.php';
    FS_Services::init();
}

if (FS_Module_Manager::can_load_module('teams')) {
    require_once dirname( __FILE__ ) . '/teams/teams.php';
    FS_Teams::init();
}

if (FS_Module_Manager::can_load_module('publications')) {
    require_once dirname( __FILE__ ) . '/publications/publications.php';
    FS_Publications::init();
}

if (FS_Module_Manager::can_load_module('portfolio')) {
    require_once dirname( __FILE__ ) . '/portfolio/portfolio.php';
    FS_Portfolio::init();
}

if (FS_Module_Manager::can_load_module('testimonials')) {
    require_once dirname( __FILE__ ) . '/testimonials/testimonials.php';
    FS_Testimonials::init();
}

if (FS_Module_Manager::can_load_module('image-gallery')) {
    require_once dirname( __FILE__ ) . '/image-gallery/image-gallery.php';
    FS_Image_Gallery::init();
}

if (FS_Module_Manager::can_load_module('resources')) {
    require_once dirname( __FILE__ ) . '/resources/resources.php';
    FS_Resources::init();
}

if (FS_Module_Manager::can_load_module('member-directory')) {
    require_once dirname( __FILE__ ) . '/member-directory/member-directory.php';
    FS_Member_Directory::init();
}

/**
 * Standalone Modules
 * These have unique functionality not based on CPTs.
 */

if (FS_Module_Manager::can_load_module('news-archives')) {
    require_once dirname( __FILE__ ) . '/news-archives/news-archives.php';
    FS_News_Archives::init();
}
