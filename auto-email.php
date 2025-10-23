<?php
/*
 * Plugin Name: EMAIL
 */
// TODO Create plugin header
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

use DoroteoDigital\AutoEmail\admin\SettingsPage;

$settings_page = new SettingsPage();

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

add_action( 'admin_enqueue_scripts', 'auto_email_settings' );
