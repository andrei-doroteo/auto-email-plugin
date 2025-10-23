<?php
declare( strict_types=1 );
/**
 * Settings page for the Auto-Email plugin
 *
 * This file contains the SettingsPage class that is responsible for
 * the WordPress Settings UI for the Auto-Email plugin. It allows users
 * to configure custom Email notifications.
 *
 * @package AutoEmail\admin
 */

namespace DoroteoDigital\AutoEmail\admin;

/**
 * Summary of SettingsPage
 *
 * Represents the Auto-Email WordPress admin panel settings.
 *
 * @version 1.0.0
 *
 * @var string $page_title
 * @var string $menu_title
 * @var string $capability
 * @var string $menu_slug
 * @var string $icon_url
 * @var int|float $position
 *
 * @since 1.0.0 ---- class created
 */
class SettingsPage {
	private string $page_title = "Page Title";
	private string $menu_title = "Email";
	private string $capability = "publish_pages";
	private string $menu_slug = "auto-email-settings";
	private string $icon_url = "dashicons-admin-plugins";
	private int|float $position = 60;

	/**
	 * Summary of __construct
	 *
	 * Adds Auto-SMS menu to the WordPress admin page
	 *
	 */
	public function __construct() {
		$this->register_admin_menu();
	}

	private function register_admin_menu(): void {
		add_action( 'admin_menu', function (): void {

			add_menu_page(
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->menu_slug,
				function (): void {
					require __DIR__ . '/templates/SettingsMenu.php';
				},
				$this->icon_url,
				$this->position,
			);
		} );
	}
}
