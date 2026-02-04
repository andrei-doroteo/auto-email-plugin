<?php
// TODO: This is a rough draft.
namespace DoroteoDigital\AutoEmail\api;

use DoroteoDigital\AutoEmail\admin\PluginOptions;
use DoroteoDigital\AutoEmail\templates\exceptions\TemplateException;
use DoroteoDigital\AutoEmail\templates\TemplateName;
use DoroteoDigital\AutoEmail\templates\Templates;
use WP_REST_Request;

/**
 * This class must be initialized at the entry point of the plugin.
 */
class Api {

	private string $base_path;
	private PluginOptions $plugin_options;
	private Templates $templates;

	/**
	 * Initializes Api object's base_endpoint, plugin_options,
	 * sender, and parser. Then register's a WordPress REST API
	 * route at initialized base_endpoint.
	 */
	/*
	 * TODO:
	 *      - Define global PLUGIN_PATH in entry point and use it here
	 */
	function __construct() {
		$this->base_path      = "/auto-email/v1";
		$this->plugin_options = PluginOptions::getInstance();
		$this->templates      = new Templates( rtrim( plugin_dir_path( __DIR__ ), '/' ) );

		$this->register_send_email_endpoint();
	}

	/**
	 * @return void
	 *
	 * Register's the '/auto-email/v1/submit/register-form' endpoint to the WordPress REST API.
	 */
	function register_send_email_endpoint(): void {

		$register_form_route = "/submit/register-form";

		add_action( 'rest_api_init', function () use ( $register_form_route ) {

			register_rest_route( $this->base_path, $register_form_route, [
				'methods'             => [ 'POST', 'OPTIONS' ],
				'callback'            => [ $this, "send_email" ],
				'permission_callback' => '__return_true', // public endpoint
			] );
		} );
	}


	/**
	 * @param $request
	 *
	 * Takes HTTP request with email details data
	 * and sends given email to given email address.
	 */
	/*
	 * TODO:
	 *      - Change endpoint to email-notify
	 */
	function send_email( WP_REST_Request $request ): void {
		$data = $request->get_params();

		if ( ! isset( $data['first_name'], $data['last_name'], $data['email'], $data['phone'], $data['class'] ) ) {
			wp_send_json_error( [ "error" => "missing_fields", "given_fields" => $data ], 400 );

			return;
		}

		try {
			$customer_email = $this->templates->get( TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION, [ "name" => $data['first_name'] ], [
				$this,
				'fallback'
			] );
			$owner_email    = $this->templates->get( TemplateName::OWNER_REGISTRATION_NOTIFICATION, [
				'name'                  => "{$data['first_name']} {$data['last_name']}",
				'class'                 => $data['class'],
				'registration_datetime' => date( "m-d-Y" ),
				'email'                 => $data['email'],
				'phone'                 => $data['phone']
			], [
				$this,
				'fallback'
			] );
		} catch ( TemplateException $e ) {
			wp_send_json_error( [ "error" => "$e" ], 500 );

			return;
		}

		add_filter( 'wp_mail_content_type', function () {
			return 'text/html';
		} );
		$customer_success = wp_mail( $data['email'], "Go-Diva's Pole Dance For Fitness", $customer_email );
		$owner_success    = wp_mail( $this->plugin_options->get_business_owner_email(), "Go-Diva's Pole Dance For Fitness", $owner_email );
		if ( ! $customer_success || ! $owner_success ) {
			wp_send_json_error( [ "error" => "one or more emails failed to send." ], 500 );

			return;
		}

		wp_send_json_success( [ "message" => "emails sent successfully." ] );


	}

	function fallback( string $s ): string {
		return "";
	}
}