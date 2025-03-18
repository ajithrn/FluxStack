<?php
/**
 * Module Loader
 *
 * @package FluxStack
 */

// Bricks customization module
require_once dirname( __FILE__ ) . '/bricks/bricks.php';

// Dynamic snippets module
require_once dirname( __FILE__ ) . '/dynamic-snippets/dynamic-snippets.php';

// Utility functions
require_once dirname( __FILE__ ) . '/utility-functions/utility-functions.php';

// Theme options
require_once dirname( __FILE__ ) . '/theme-options/theme-options.php';

// Testimonials module
require_once dirname( __FILE__ ) . '/testimonials/testimonials.php';

// Image Gallery module
require_once dirname( __FILE__ ) . '/image-gallery/image-gallery.php';

// White label module
require_once dirname(__FILE__) . '/white-label/white-label.php';

// Teams module
require_once dirname(__FILE__) . '/teams/teams.php';

// Publications module
require_once dirname(__FILE__) . '/publications/publications.php';

// News Archives module
require_once dirname(__FILE__) . '/news-archives/news-archives.php';

// Initialize modules
FS_Bricks::init();
FS_Utils::init();
FS_Theme_Options::init();
FS_Dynamic_Snippets::init();
FS_Image_Gallery::init();
FS_Testimonials::init();
FS_White_Label::init();
FS_Teams::init();
FS_Publications::init();
FS_News_Archives::init();