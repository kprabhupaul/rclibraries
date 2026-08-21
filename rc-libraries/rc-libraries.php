<?php
/**
 * Plugin Name: RC Libraries
 * Description: Manage and enqueue CSS and JavaScript libraries on the WordPress frontend and backend.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RC_LIBRARIES_VERSION', '1.0.0' );
define( 'RC_LIBRARIES_PATH', plugin_dir_path( __FILE__ ) );
define( 'RC_LIBRARIES_URL', plugin_dir_url( __FILE__ ) );

require_once RC_LIBRARIES_PATH . 'functions.php';
