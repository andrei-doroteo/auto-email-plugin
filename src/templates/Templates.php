<?php declare( strict_types=1 );

namespace DoroteoDigital\AutoEmail\templates;

use DoroteoDigital\AutoEmail\templates\exceptions\TemplateNotFoundException;
use DoroteoDigital\AutoEmail\templates\exceptions\TemplateFileException;
use DoroteoDigital\AutoEmail\templates\exceptions\TemplateRenderException;
use DoroteoDigital\AutoEmail\templates\exceptions\TemplateFileEmptyException;

/**
 * This file serves as an abstraction to get filled in email templates.
 */

/**
 * Enum representing available email templates.
 *
 * Each case holds the path to it's template file from the `src/` directory.
 * Use this enum when calling Templates::get() to ensure type safety.
 */
enum TemplateName: string {
	case CUSTOMER_REGISTRATION_NOTIFICATION = "templates/client-confirmation.template.html";
	case OWNER_REGISTRATION_NOTIFICATION = "templates/owner-registration-confirmation.template.html";
	case CUSTOMER_CONTACT_NOTIFICATION = "templates/client-contact-form.template.html";
	case OWNER_CONTACT_NOTIFICATION = "templates/owner-contact-form.template.html";

	/**
	 * Get the file path for a given template.
	 *
	 * @return string Path to the template file from the `src/` directory.
	 */
	public function getPath(): string {
		return $this->value;
	}
}

/**
 * Utility class to get pre-made stringified email templates from disk.
 *
 *
 * Example Usage:
 *      $templates = new Templates(base_path);
 *      $templates->get(TemplateName::TEMPLATE_NAME_HERE);
 */
class Templates {
	/*
	   TODO:
		   - Implement a cache for each request to optimize
		   for multiple template file reads in one request.
	*/

	/**
	 * Regex pattern to match template variables in the format {{variable_name}}
	 * Allows optional whitespace around the variable name.
	 */
	private const TEMPLATE_VAR_PATTERN = '/\{\s*\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\}\s*\}/';

	/**
	 * The base path for the template.html files.
	 *
	 * This is prepended to all file paths passed into
	 * methods for this class.
	 * @var string
	 */
	private string $base_path;

	/**
	 * Initializes a Templates object with the given
	 * $base_path.
	 *
	 * @param string $base_path
	 */
	public function __construct( string $base_path ) {
		$this->base_path = $base_path;
	}

	/**
	 * Returns the filled email template specified with $template.
	 * - If there are template vars with no given replacement
	 * they are replaced with $fallback.
	 *
	 * Example Usage: Template->get(TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION, ["name" => "Jane"], function (string $s):string {return "User";});
	 *
	 * @param TemplateName $template The template enum case to use.
	 * @param array $template_variables An associative array of key-value pairs for template vars.
	 * @param ?callable $fallback A function that is passed all the unreplaced vars.
	 *
	 * @return string The rendered template with variables replaced.
	 *
	 * @throws TemplateFileException If the template file cannot be read from disk.
	 * @throws TemplateRenderException If template variable replacement fails.
	 */
	public function get(
		TemplateName $template,
		array $template_variables,
		?callable $fallback = null
	): string {
		// Set default fallback callable if none provided
		if ( $fallback === null ) {
			$fallback = function ( string $var ): string {
				return "";
			};
		}

		// Get the raw template from disk
		$raw_template = $this->get_raw_template( $template->getPath() );

		// Replace template variables and return the result
		return $this->fill_template_variables( $raw_template, $template_variables, $fallback );
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
	 * @throws TemplateFileEmptyException If the file is empty.
	 */
	private function get_raw_template( string $file_path ): string {
		$full_path = "$this->base_path/$file_path";

		// Attempt to read the file
		$content = @file_get_contents( $full_path );

		// If file_get_contents returns false, it means the file couldn't be read
		if ( $content === false ) {
			throw new TemplateFileException( "Failed to read template file: $full_path" );
		}

		// Check if the file is empty
		if ( empty( $content ) ) {
			throw new TemplateFileEmptyException( "Template file is empty: $file_path" );
		}

		return $content;
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
	 * @throws TemplateRenderException If provided template variable map has unused variables.
	 */
	private function fill_template_variables( string $template, array $template_vars, callable $fallback ): string {
		// If template is empty, return empty string
		if ( empty( $template ) ) {
			return "";
		}

		// Track which variables from $template_vars are used
		$used_vars = array();

		// Replace all template variables
		$result = preg_replace_callback(
			self::TEMPLATE_VAR_PATTERN,
			function ( $matches ) use ( $template_vars, $fallback, &$used_vars ) {
				$var_name = $matches[1]; // The captured variable name without braces

				// Check if this variable has a replacement in $template_vars
				if ( array_key_exists( $var_name, $template_vars ) ) {
					$used_vars[ $var_name ] = true;

					return $template_vars[ $var_name ];
				}

				// Otherwise, use the fallback function
				return $fallback( $var_name );
			},
			$template
		);

		// Check if all variables in $template_vars were used
		foreach ( array_keys( $template_vars ) as $key ) {
			if ( ! isset( $used_vars[ $key ] ) ) {
				throw new TemplateRenderException( "Template variable '$key' was provided but not found in template" );
			}
		}

		return $result;
	}

	/**
	 * Parses template contents for template variables and adds them to an array.
	 * Only one of each template variable will be in that array. This will result
	 * in an array of all the available template variables
	 *
	 * This function is primarily useful in writing tests for templates.
	 *
	 * @param TemplateName $template
	 *
	 * @return array Array of unique template variable names (without braces).
	 */
	public function get_template_vars( TemplateName $template ): array {
		// Get the raw template content
		$templateContent = $this->get_raw_template( $template->getPath() );

		// Find all template variables using regex pattern
		preg_match_all( self::TEMPLATE_VAR_PATTERN, $templateContent, $matches );

		// $matches[1] contains the captured variable names without braces
		// Return unique values only and re-index the array
		return array_values( array_unique( $matches[1] ) );
	}
}