<?php
/**
 * Theme functions and definitions
 *
 * @package FluxStack
 */

// GitHub repo for auto-updates
if (!defined('FLUXSTACK_GITHUB_REPO')) {
    define('FLUXSTACK_GITHUB_REPO', 'ajithrn/FluxStack');
}

// Load modules from child theme
require_once dirname( __FILE__ ) . '/modules/modules.php';

// Load Native blocks
require_once dirname( __FILE__ ) . '/native-blocks/native-blocks.php';
