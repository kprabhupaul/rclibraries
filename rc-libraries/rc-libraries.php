<?php
/**
 * Plugin Name:       RC Libraries
 * Description:       Manage and enqueue RC Libraries (CSS & JavaScript) on the WordPress frontend and backend.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.1
 * Author:            Pratap Kumar Kotti
 * Author URI:        https://github.com/kprabhupaul
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       rc-libraries
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RC_LIBRARIES_VERSION', '1.0.0' );
define( 'RC_LIBRARIES_PATH', plugin_dir_path( __FILE__ ) );
define( 'RC_LIBRARIES_URL', plugin_dir_url( __FILE__ ) );

require_once RC_LIBRARIES_PATH . 'functions.php';
require_once RC_LIBRARIES_PATH . 'ajax-callbacks.php';
require_once RC_LIBRARIES_PATH . 'pages.php';