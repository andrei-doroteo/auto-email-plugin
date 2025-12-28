<?php

namespace DoroteoDigital\AutoEmail\admin;

final class PluginOptions {

	private static ?PluginOptions $singleton = null;
	private const string WP_OPTIONS_KEY = 'auto_email__go-divas_options';
	private array $plugin_options;
	private static array $default_plugin_options = [
		"automatic_notifs" => [
			"business_owner_email" => "",
		]
	];

	private function __construct() {
		$plugin_options = get_option( self::WP_OPTIONS_KEY );
		if ( ! $plugin_options ) {
			add_option( self::WP_OPTIONS_KEY, self::$default_plugin_options );
			$plugin_options = self::$default_plugin_options;
		}
		$this->plugin_options = $plugin_options;
	}

	public static function getInstance(): PluginOptions {
		if ( ! self::$singleton ) {
			self::$singleton = new PluginOptions();
		}

		return self::$singleton;
	}

	private function __clone() {
		// Prevent cloning of the singleton
	}

	public function get_business_owner_email(): string {
		// !!! TODO: add error logging in case of array access error
		return $this->plugin_options['automatic_notifs']['business_owner_email'] ?? "";
	}

	public function set_business_owner_email( string $email ): void {
		$this->plugin_options['automatic_notifs']['business_owner_email'] = $email;
		$this->save_options();
	}

	// !!! TODO: Implement a solution so all options are not
	//           saved anytime one option is saved
	private function save_options(): void {
		update_option( self::WP_OPTIONS_KEY, $this->plugin_options );
	}

}