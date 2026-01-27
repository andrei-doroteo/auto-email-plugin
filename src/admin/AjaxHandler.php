<?php
/**
 * This file handles admin-ajax.php actions for the AutoEmail plugin.
 */

namespace DoroteoDigital\AutoEmail\admin;

/**
 * Represents a registration handler for WordPress admin-ajax
 * actions for the AutoEmail plugin.
 *
 * This class must be initialized at the entry point of the WordPress plugin.
 */
class AjaxHandler {

	/**
	 * Creates an AjaxHandler which registers the admin-ajax actions
	 * for the AutoEmail plugin upon initialization.
	 */

	// TODO: Test constructor
	public function __construct() {
		$this->register_save_options_action();
		$this->register_get_business_owner_email_action();
	}

	/**
	 * @since 1.0.0
	 * Registers the autoemail_save_options admin-ajax action.
	 *
	 * The autoemail_save_options action updates the plugin options
	 * based on the given business_owner_email values of
	 * the POST request.
	 *
	 * POST request requirements:
	 * - x-www-form-urlencoded formatted body.
	 * - A valid WordPress Nonce or returns permission denied (i.e. security=<nonce>).
	 * - Editor Permissions or higher or returns permission denied (based on the authenticated user).
	 *
	 * Endpoint url: POST <base url>/wp-admin/admin-ajax.php
	 *
	 * @version
	 * 1.0.0
	 *
	 */

	// TODO: - Test function
	private function register_save_options_action() {
		add_action( 'wp_ajax_autoemail_save_options', function () {

			if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
				wp_send_json_error( 'Invalid request method' );

				return;
			}

			// Check nonce for security
			check_ajax_referer( 'autoemail_settings_nonce', 'security' );

			// Check user permissions
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'Permission denied' );

				return;
			}

			$plugin_options = PluginOptions::getInstance();

			// Check for business_owner_email
			if ( ! isset( $_POST['business_owner_email'] ) ) {
				wp_send_json_error( 'business_owner_email must be set' );

				return;
			}

			// Sanitize and save business owner email
			$business_owner_email = sanitize_text_field( $_POST['business_owner_email'] );
			$plugin_options->set_business_owner_email( $business_owner_email );
			wp_send_json_success( 'Settings saved successfully' );

		} );
	}

	/**
	 * Endpoint url: <base url>/wp-admin/admin-ajax.php?action=get_business_owner_email
	 */
	private function register_get_business_owner_email_action() {
		add_action( 'wp_ajax_get_business_owner_email', function () {

			// Check request method
			if ( $_SERVER['REQUEST_METHOD'] !== 'GET' ) {
				wp_send_json_error( 'Invalid request method' );

				return;
			}

			// Check nonce for security
			check_ajax_referer( 'autoemail_settings_nonce', 'security' );

			// Check user permissions
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'Permission denied' );

				return;
			}

			$plugin_options = PluginOptions::getInstance();
			wp_send_json( [ 'business_owner_email' => $plugin_options->get_business_owner_email() ] );

		} );
	}
}