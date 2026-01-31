<?php declare( strict_types=1 );

namespace DoroteoDigital\AutoEmail\templates;

use DoroteoDigital\AutoEmail\templates\exceptions\TemplateNotFoundException;
use DoroteoDigital\AutoEmail\templates\exceptions\TemplateFileException;
use DoroteoDigital\AutoEmail\templates\exceptions\TemplateRenderException;

/**
 * This file serves as an abstraction to get filled in email templates.
 */
class Templates {
	/*
	   TODO:
		   - Implement a cache for each request to optimize
		   for multiple template file reads in one request.
	*/

	/**
	 * A map from the usable template names to the paths of the files from src/
	 * @var array
	 */
	private const array TEMPLATES = [
		"notifications/client" => "templates/client-confirmation.template.html",
		"notifications/owner"  => "templates/owner-registration-confirmation.template.html",
	];

	public function __construct() {
	}

	/**
	 * Returns the filled email template specified with $template.
	 * - If the template does not exist an error is thrown.
	 * - If there are template vars with no given replacement
	 * they are replaced with $fallback.
	 *
	 * Example Usage: Templates::get("notifications/client", ["name" => "Jane"], function (string $s):string {return "User";});
	 *
	 * @param string $template The name of the template required.
	 * @param array $template_variables An associative array of key-value pairs for template vars.
	 * @param ?callable $fallback A function that is passed all the unreplaced vars.
	 *
	 * @return string The rendered template with variables replaced.
	 *
	 * @throws TemplateNotFoundException If the requested template name doesn't exist in TEMPLATES.
	 * @throws TemplateFileException If the template file cannot be read from disk.
	 * @throws TemplateRenderException If template variable replacement fails.
	 */
	public static function get(
		string $template,
		array $template_variables,
		?callable $fallback = null
	): string {
		// Set default fallback if none provided
		if ( $fallback === null ) {
			$fallback = function ( string $var ): string {
				return "";
			};
		}

		if ( ! isset( self::TEMPLATES[ $template ] ) ) {
			// throw an error
			return ""; // stub
		}

		// get file
		// replace template vars
		// return result

		return ""; // stub
	}

	/**
	 * Reads file from disk and returns it as a string.
	 * $file_path should not have a prepended "/"
	 *
	 * If file cannot be read from disk, throws an error.
	 *
	 * @param string $file_path Path to a file from `<project root>/src`
	 *
	 * @return string The raw template content.
	 *
	 * @throws TemplateFileException If the file doesn't exist or cannot be read.
	 */
	private static function get_raw_template( string $file_path ): string {
		return file_get_contents( plugin_dir_path( __DIR__ ) . $file_path ); // stub
	}

	/**
	 * Replaces all template variables with respective key-value pair in $template_vars.
	 *
	 * - If $template is an empty string, returns empty string.
	 * - Any remaining template vars are passed into $fallback and for replacement.
	 *
	 * @param string $template A string representing a template.
	 * @param array $template_vars An associative array with template variables as keys and replacements as values.
	 * @param callable $fallback A function that takes a string and returns a string.
	 *
	 * @return string The given template with template variables replaced.
	 *
	 * @throws TemplateRenderException If template variable replacement fails.
	 */
	private static function fill_template_variables( string $template, array $template_vars, callable $fallback ): string {
		return ""; // stub
	}
}