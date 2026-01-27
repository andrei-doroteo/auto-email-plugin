<?php
/*
 * Plugin Name: GO-DIVAS | AUTO EMAIL
 */

/* TODO: - Create plugin header
 *       - Add conditional loading so everything
 *         doesn't run every WordPress request.
 *       - Use hooks for WordPress to handle conditional loading.
*/

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

use DoroteoDigital\AutoEmail\admin\SettingsPage;
use DoroteoDigital\AutoEmail\api\Api;
use DoroteoDigital\AutoEmail\admin\AjaxHandler;

// Initialize plugin classes
new SettingsPage();
new Api();
new AjaxHandler();
function auto_email_settings( $admin_page ): void {
	if ( 'toplevel_page_auto-email-settings' !== $admin_page ) {
		return;
	}

	$asset_file = plugin_dir_path( __FILE__ ) . 'build/index.asset.php';

	if ( ! file_exists( $asset_file ) ) {
		return;
	}

	$asset = include $asset_file;

	wp_enqueue_script(
		'auto-email-js',
		plugins_url( 'build/index.js', __FILE__ ),
		$asset['dependencies'],
		$asset['version'],
		[ 'in_footer' => true ]
	);
}

function auto_email_styles(): void {
	wp_enqueue_style(
		'auto-email-css',
		plugins_url( 'build/index.css', __FILE__ ),
		[],
	);
}

function add_cors_http_header() {
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, OPTIONS');
	header('Access-Control-Allow-Headers: Content-Type, Authorization');
	header('Access-Control-Max-Age: 86400'); // 24hrs
}

add_action('init', 'add_cors_http_header');

$api = new Api();

add_action( 'admin_enqueue_scripts', 'auto_email_settings' );
add_action( 'admin_enqueue_scripts', 'auto_email_styles' );
