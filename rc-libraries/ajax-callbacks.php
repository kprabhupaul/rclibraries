<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/*
 * AJAX: Save Frontend / Backend settings.
 */
function rc_libraries_ajax_save_settings() {

	check_ajax_referer( 'rc_libraries_save_settings', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error(
			array(
				'message' => 'You are not allowed to do this.',
			),
			403
		);
	}

	$posted = isset( $_POST['rc_libraries'] ) && is_array( $_POST['rc_libraries'] )
	? map_deep( wp_unslash( $_POST['rc_libraries'] ), 'sanitize_text_field' )
	: array();
	
	$libraries = rc_libraries_get_libraries();

	foreach ( $libraries as $library_id => $library ) {

		$libraries[ $library_id ]['frontend'] = ! empty(
			$posted[ $library_id ]['frontend']
		);

		$libraries[ $library_id ]['backend'] = ! empty(
			$posted[ $library_id ]['backend']
		);
	}

	update_option( 'rc_libraries', $libraries );

	wp_send_json_success(
		array(
			'message' => 'Library settings saved successfully.',
		)
	);
}
add_action( 'wp_ajax_rc_libraries_save_settings', 'rc_libraries_ajax_save_settings' );
