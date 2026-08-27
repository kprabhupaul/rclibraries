<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/*
 * Main RC Libraries page.
 */
function rc_libraries_admin_page() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$libraries = rc_libraries_get_libraries();
	?>

	<div class="wrap">

		<h1>RC Libraries</h1>

		<form method="post" id="rc-libraries-settings-form">

			<table class="widefat striped" style="margin-top:20px;">

				<thead>
					<tr>
						<th>Name</th>
						<th style="width:100px; text-align:center;">Frontend</th>
						<th style="width:100px; text-align:center;">Backend</th>
					</tr>
				</thead>

				<tbody>

					<?php foreach ( $libraries as $library_id => $library ) : ?>

						<tr>

							<td>
								<?php echo esc_html( $library['name'] ); ?>
							</td>

							<td style="text-align:center;">
								<input
									type="checkbox"
									name="rc_libraries[<?php echo esc_attr( $library_id ); ?>][frontend]"
									value="1"
									<?php checked( $library['frontend'] ); ?>
								>
							</td>

							<td style="text-align:center;">
								<input
									type="checkbox"
									name="rc_libraries[<?php echo esc_attr( $library_id ); ?>][backend]"
									value="1"
									<?php checked( $library['backend'] ); ?>
								>
							</td>

						</tr>

					<?php endforeach; ?>

					<?php if ( empty( $libraries ) ) : ?>

						<tr>
							<td colspan="3">No default libraries found.</td>
						</tr>

					<?php endif; ?>

				</tbody>

			</table>

			<p>
				<button
					type="submit"
					class="button button-primary"
					id="rc-libraries-save-button"
				>
					Save Changes
				</button>
			</p>

		</form>

		<script>
		function rcl_save_library_settings() {
			const form = document.getElementById('rc-libraries-settings-form');
			const button = document.getElementById('rc-libraries-save-button');

			button.disabled = true;

			const formData = new FormData(form);
			formData.append('action', 'rc_libraries_save_settings');
			formData.append('nonce', <?php echo wp_json_encode( wp_create_nonce( 'rc_libraries_save_settings' ) ); ?>);

			fetch(
				<?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>,
				{
					method: 'POST',
					body: formData,
					credentials: 'same-origin'
				}
			)
			.then(function (response) {
				return response.json();
			})
			.then(function (result) {
				if (!result.success) {
					throw new Error(
						result.data && result.data.message
							? result.data.message
							: 'Unable to save library settings.'
					);
				}

				alert(result.data.message);
				button.disabled = false;
			})
			.catch(function (error) {
				alert(error.message || 'Unable to save library settings.');
				button.disabled = false;
			});
		}

		document.getElementById('rc-libraries-settings-form').addEventListener('submit', function (event) {
			event.preventDefault();
			rcl_save_library_settings();
		});
		</script>

	</div>

	<?php
}
