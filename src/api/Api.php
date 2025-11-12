<?php
// TODO: This is a rough draft.
namespace DoroteoDigital\AutoEmail\api;

use DoroteoDigital\AutoEmail\parser\Parser;
use WP_REST_Response;
use DoroteoDigital\AutoEmail\admin\PluginOptions;
use DoroteoDigital\AutoEmail\sender\Sender;

class Api {

	private string $base_endpoint;
	private PluginOptions $plugin_options;
	private Sender $sender;
	private Parser $parser;

	/**
	 * Initializes Api object's base_endpoint, plugin_options,
	 * sender, and parser. Then register's a WordPress REST API
	 * route at initialized base_endpoint.
	 */
	function __construct() {
		$this->base_endpoint  = "auto-email/v1/";
		$this->plugin_options = PluginOptions::getInstance();
		$this->sender         = new Sender();
		$this->parser         = new Parser();

		$this->register_send_email_endpoint();
	}

	/**
	 * @return void
	 *
	 * Register's the '/auto-email/v1/send-mail/' endpoint to the WordPress REST API.
	 */
	function register_send_email_endpoint(): void {

		$endpoint_route = "/send-email";

		add_action( 'rest_api_init', function () use ( $endpoint_route ) {

			register_rest_route( $this->base_endpoint, $endpoint_route, [
				'methods'             => 'POST',
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
	function send_email( $request ) {

	}
}