<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/*
 * Get saved default libraries.
 */
function rc_libraries_get_libraries() {

	$libraries = get_option( 'rc_libraries', array() );

	if ( ! is_array( $libraries ) ) {
		return array();
	}

	foreach ( $libraries as $library_id => $library ) {

		if ( ! is_array( $library ) ) {
			unset( $libraries[ $library_id ] );
			continue;
		}

		$dependencies = array();

		if ( ! empty( $library['dependencies'] ) && is_array( $library['dependencies'] ) ) {
			foreach ( $library['dependencies'] as $dependency ) {
				$dependency = sanitize_key( $dependency );

				if ( '' !== $dependency ) {
					$dependencies[] = $dependency;
				}
			}
		}

		$libraries[ $library_id ] = array(
			'name'         => isset( $library['name'] ) ? (string) $library['name'] : '',
			'url'          => isset( $library['url'] ) ? esc_url_raw( $library['url'] ) : '',
			'file'         => isset( $library['file'] ) ? (string) $library['file'] : '',
			'frontend'     => ! empty( $library['frontend'] ),
			'backend'      => ! empty( $library['backend'] ),
			'type'         => isset( $library['type'] ) && in_array( $library['type'], array( 'js', 'css' ), true ) ? $library['type'] : '',
			'dependencies' => array_values( array_unique( $dependencies ) ),
		);
	}

	uksort(
		$libraries,
		function ( $a, $b ) use ( $libraries ) {
			return strnatcasecmp(
				$libraries[ $a ]['name'] ?? $a,
				$libraries[ $b ]['name'] ?? $b
			);
		}
	);

	return $libraries;
}


/*
 * Add RC Libraries default libraries on plugin activation.
 */
function rc_libraries_add_default_libraries() {

	$defaults = array(
		'fexios' => array(
			'name'         => 'Fexios',
			'url'          => RC_LIBRARIES_URL . 'default-libraries/fexios.js',
			'file'         => 'default-libraries/fexios.js',
			'frontend'     => true,
			'backend'      => true,
			'type'         => 'js',
			'dependencies' => array(),
		),
		'simal-css' => array(
			'name'         => 'Simal CSS',
			'url'          => RC_LIBRARIES_URL . 'default-libraries/simal.css',
			'file'         => 'default-libraries/simal.css',
			'frontend'     => true,
			'backend'      => true,
			'type'         => 'css',
			'dependencies' => array(),
		),
		'simal-js' => array(
			'name'         => 'Simal JS',
			'url'          => RC_LIBRARIES_URL . 'default-libraries/simal.js',
			'file'         => 'default-libraries/simal.js',
			'frontend'     => true,
			'backend'      => true,
			'type'         => 'js',
			'dependencies' => array(),
		),
	);

	$libraries = get_option( 'rc_libraries', array() );

	if ( ! is_array( $libraries ) ) {
		$libraries = array();
	}

	foreach ( $defaults as $library_id => $default ) {
		if ( ! isset( $libraries[ $library_id ] ) ) {
			$libraries[ $library_id ] = $default;
		}
	}

	update_option( 'rc_libraries', $libraries );
}


/*
 * Register RC Libraries admin menu.
 */
function rc_libraries_admin_menu() {

	add_menu_page(
		'RC Libraries',
		'RC Libraries',
		'manage_options',
		'rc-libraries',
		'rc_libraries_admin_page',
		'dashicons-editor-code',
		66
	);
}
add_action( 'admin_menu', 'rc_libraries_admin_menu' );


/*
 * Enqueue enabled default libraries.
 */
function rc_libraries_enqueue() {

	$libraries = rc_libraries_get_libraries();
	$context   = is_admin() ? 'backend' : 'frontend';
	$enqueued  = array();

	$enqueue_library = function ( $library_id ) use ( &$enqueue_library, &$libraries, &$enqueued ) {

		if ( isset( $enqueued[ $library_id ] ) || ! isset( $libraries[ $library_id ] ) ) {
			return;
		}

		$library = $libraries[ $library_id ];

		if ( empty( $library['file'] ) || empty( $library['type'] ) ) {
			return;
		}

		$file_path = RC_LIBRARIES_PATH . ltrim( $library['file'], '/\\' );

		if ( ! is_file( $file_path ) ) {
			return;
		}

		$enqueued[ $library_id ] = true;

		$dependencies = array();

		foreach ( $library['dependencies'] as $dependency_id ) {

			if ( isset( $libraries[ $dependency_id ] ) ) {

				$enqueue_library( $dependency_id );

				if ( 'js' === $library['type'] && 'js' === $libraries[ $dependency_id ]['type'] ) {
					$dependencies[] = 'rc-' . $dependency_id;
				}

			} else {
				$dependencies[] = $dependency_id;
			}
		}

		$handle  = 'rc-' . sanitize_key( $library_id );
		$version = filemtime( $file_path );

		if ( 'js' === $library['type'] ) {

			wp_enqueue_script(
				$handle,
				$library['url'],
				array_values( array_unique( $dependencies ) ),
				$version,
				false
			);

		} elseif ( 'css' === $library['type'] ) {

			wp_enqueue_style(
				$handle,
				$library['url'],
				array(),
				$version
			);
		}
	};

	foreach ( $libraries as $library_id => $library ) {

		if ( empty( $library[ $context ] ) ) {
			continue;
		}

		$enqueue_library( $library_id );
	}
}
add_action( 'wp_enqueue_scripts', 'rc_libraries_enqueue' );
add_action( 'admin_enqueue_scripts', 'rc_libraries_enqueue' );
