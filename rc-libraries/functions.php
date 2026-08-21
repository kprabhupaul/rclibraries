<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/*
 * Get available libraries.
 */
function rc_libraries_get_libraries() {

	$libraries_path = RC_LIBRARIES_PATH . 'libraries';
	$libraries     = array();

	if ( ! is_dir( $libraries_path ) ) {
		return $libraries;
	}

	$folders = glob( $libraries_path . '/*', GLOB_ONLYDIR );

	if ( ! $folders ) {
		return $libraries;
	}

	foreach ( $folders as $folder ) {

		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(
				$folder,
				FilesystemIterator::SKIP_DOTS
			)
		);

		$has_assets = false;

		foreach ( $files as $file ) {
			if ( $file->isFile() && in_array( strtolower( $file->getExtension() ), array( 'js', 'css' ), true ) ) {
				$has_assets = true;
				break;
			}
		}

		if ( $has_assets ) {
			$libraries[] = basename( $folder );
		}
	}

	sort( $libraries, SORT_NATURAL | SORT_FLAG_CASE );

	return $libraries;
}


/*
 * Get saved settings.
 */
function rc_libraries_get_settings() {

	$settings = get_option( 'rc_libraries_settings', array() );
	$libraries = rc_libraries_get_libraries();

	foreach ( $libraries as $library ) {

		if ( ! isset( $settings[ $library ] ) || ! is_array( $settings[ $library ] ) ) {
			$settings[ $library ] = array(
				'frontend' => false,
				'backend'  => false,
			);
		}
	}

	return $settings;
}


/*
 * Register RC Libraries menu.
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
 * Save settings.
 */
function rc_libraries_save_settings() {

	if ( ! isset( $_POST['rc_libraries_save'] ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	check_admin_referer( 'rc_libraries_save_settings', 'rc_libraries_nonce' );

	$posted    = isset( $_POST['rc_libraries'] ) ? wp_unslash( $_POST['rc_libraries'] ) : array();
	$libraries = rc_libraries_get_libraries();
	$settings  = array();

	foreach ( $libraries as $library ) {
		$settings[ $library ] = array(
			'frontend' => ! empty( $posted[ $library ]['frontend'] ),
			'backend'  => ! empty( $posted[ $library ]['backend'] ),
		);
	}

	update_option( 'rc_libraries_settings', $settings );

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'            => 'rc-libraries',
				'settings-updated' => '1',
			),
			admin_url( 'admin.php' )
		)
	);

	exit;
}
add_action( 'admin_init', 'rc_libraries_save_settings' );


/*
 * Admin page.
 */
function rc_libraries_admin_page() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$libraries = rc_libraries_get_libraries();
	$settings  = rc_libraries_get_settings();
	?>

	<div class="wrap">

		<h1>RC Libraries</h1>

		<?php if ( isset( $_GET['settings-updated'] ) ) : ?>
			<div class="notice notice-success is-dismissible">
				<p>Settings saved successfully.</p>
			</div>
		<?php endif; ?>

		<form method="post" style="margin-top: 20px">

			<?php wp_nonce_field( 'rc_libraries_save_settings', 'rc_libraries_nonce' ); ?>

			<table class="widefat striped" style="max-width:600px;">

				<thead>
					<tr>
						<th>Library</th>
						<th style="text-align:center;">Frontend</th>
						<th style="text-align:center;">Backend</th>
					</tr>
				</thead>

				<tbody>

					<?php foreach ( $libraries as $library ) : ?>

						<tr>
							<td><?php echo esc_html( $library ); ?></td>

							<td style="text-align:center;">
								<input
									type="checkbox"
									name="rc_libraries[<?php echo esc_attr( $library ); ?>][frontend]"
									value="1"
									<?php checked( ! empty( $settings[ $library ]['frontend'] ) ); ?>
								>
							</td>

							<td style="text-align:center;">
								<input
									type="checkbox"
									name="rc_libraries[<?php echo esc_attr( $library ); ?>][backend]"
									value="1"
									<?php checked( ! empty( $settings[ $library ]['backend'] ) ); ?>
								>
							</td>
						</tr>

					<?php endforeach; ?>

					<?php if ( empty( $libraries ) ) : ?>
						<tr>
							<td colspan="3">No libraries found.</td>
						</tr>
					<?php endif; ?>

				</tbody>

			</table>

			<p>
				<?php submit_button( 'Save', 'primary', 'rc_libraries_save', false ); ?>
			</p>

		</form>

	</div>

	<?php
}


/*
 * Enqueue one library's assets.
 */
function rc_libraries_enqueue_library( $library ) {

	$library_path = RC_LIBRARIES_PATH . 'libraries/' . $library;
	$library_url  = RC_LIBRARIES_URL . 'libraries/' . $library;

	if ( ! is_dir( $library_path ) ) {
		return;
	}

	$files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator(
			$library_path,
			FilesystemIterator::SKIP_DOTS
		)
	);

	foreach ( $files as $file ) {

		if ( ! $file->isFile() ) {
			continue;
		}

		$extension = strtolower( $file->getExtension() );

		if ( ! in_array( $extension, array( 'js', 'css' ), true ) ) {
			continue;
		}

		$relative_path = str_replace(
			$library_path . DIRECTORY_SEPARATOR,
			'',
			$file->getPathname()
		);

		$relative_path = str_replace( DIRECTORY_SEPARATOR, '/', $relative_path );
		$url           = $library_url . '/' . $relative_path;
		$handle        = 'rc-' . sanitize_key( $library . '-' . str_replace( '/', '-', $relative_path ) );
		$version       = filemtime( $file->getPathname() );

		if ( 'js' === $extension ) {

			wp_enqueue_script(
				$handle,
				$url,
				array(),
				$version,
				true
			);

		} elseif ( 'css' === $extension ) {

			wp_enqueue_style(
				$handle,
				$url,
				array(),
				$version
			);

		}
	}
}


/*
 * Enqueue enabled libraries.
 */
function rc_libraries_enqueue() {

	$settings = rc_libraries_get_settings();
	$context  = is_admin() ? 'backend' : 'frontend';

	foreach ( $settings as $library => $options ) {

		if ( ! empty( $options[ $context ] ) ) {
			rc_libraries_enqueue_library( $library );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'rc_libraries_enqueue' );
add_action( 'admin_enqueue_scripts', 'rc_libraries_enqueue' );
