<?php declare( strict_types=1 );

/**
 * This file is AI generated.
 *
 * TODO: Do a manual code review on these tests.
 */

use PHPUnit\Framework\TestCase;
use DoroteoDigital\AutoEmail\templates\Templates;
use DoroteoDigital\AutoEmail\templates\TemplateName;
use DoroteoDigital\AutoEmail\templates\exceptions\TemplateFileException;
use DoroteoDigital\AutoEmail\templates\exceptions\TemplateRenderException;
use PHPUnit\Framework\Attributes\DataProvider;

class TemplatesTest extends TestCase {
	/**
	 * Test that get() returns a non-empty string when given a valid template name
	 * and all required template variables.
	 */
	public function test_get_returns_string_with_valid_template_and_variables(): void {
		$result = Templates::get(
			TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
			[
				"first_name" => "Jane",
				"last_name"  => "Doe"
			]
		);

		$this->assertIsString( $result );
		$this->assertNotEmpty( $result );
	}

	/**
	 * Test that get() successfully replaces template variables in the output.
	 */
	public function test_get_replaces_template_variables(): void {
		$result = Templates::get(
			TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
			[
				"first_name" => "John",
				"last_name"  => "Smith"
			]
		);

		$this->assertStringContainsString( "John", $result );
		$this->assertStringContainsString( "Smith", $result );
		$this->assertStringNotContainsString( "{{first_name}}", $result );
		$this->assertStringNotContainsString( "{{last_name}}", $result );
	}

	/**
	 * Test that get() works with the owner notification template.
	 */
	public function test_get_works_with_owner_template(): void {
		$result = Templates::get(
			TemplateName::OWNER_REGISTRATION_NOTIFICATION,
			[
				"first_name"            => "Jane",
				"last_name"             => "Doe",
				"email"                 => "jane@example.com",
				"phone"                 => "555-1234",
				"class"                 => "Beginner Pole",
				"registration_datetime" => "2026-02-01 10:30 AM"
			]
		);

		$this->assertIsString( $result );
		$this->assertStringContainsString( "Jane", $result );
		$this->assertStringContainsString( "Doe", $result );
		$this->assertStringContainsString( "jane@example.com", $result );
		$this->assertStringContainsString( "555-1234", $result );
		$this->assertStringContainsString( "Beginner Pole", $result );
	}

	/**
	 * Test that get() replaces multiple different template variables.
	 */
	public function test_get_replaces_multiple_variables(): void {
		$result = Templates::get(
			TemplateName::OWNER_REGISTRATION_NOTIFICATION,
			[
				"first_name"            => "Alice",
				"last_name"             => "Johnson",
				"email"                 => "alice@test.com",
				"phone"                 => "123-456-7890",
				"class"                 => "Advanced Class",
				"registration_datetime" => "2026-01-15 14:00"
			]
		);

		$this->assertStringContainsString( "Alice", $result );
		$this->assertStringContainsString( "Johnson", $result );
		$this->assertStringContainsString( "alice@test.com", $result );
		$this->assertStringContainsString( "123-456-7890", $result );
		$this->assertStringContainsString( "Advanced Class", $result );
		$this->assertStringContainsString( "2026-01-15 14:00", $result );
	}

	/**
	 * Note: With the enum-based implementation, invalid template tests are no longer
	 * needed as type safety prevents passing invalid templates at compile time.
	 * This is a benefit of using the TemplateName enum!
	 */

	/**
	 * Test that unreplaced template variables are replaced with empty string
	 * when no fallback is provided (default behavior).
	 */
	public function test_get_uses_empty_string_fallback_by_default(): void {
		$result = Templates::get(
			TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
			[] // No variables provided
		);

		// The {{first_name}} and {{last_name}} variables should be replaced with empty string
		$this->assertStringNotContainsString( "{{first_name}}", $result );
		$this->assertStringNotContainsString( "{{last_name}}", $result );
		$this->assertStringNotContainsString( "{{", $result );
	}

	/**
	 * Test that the fallback function is called for unreplaced variables.
	 */
	public function test_get_uses_custom_fallback_for_missing_variables(): void {
		$result = Templates::get(
			TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
			[], // No variables provided
			function ( string $var ): string {
				return "[MISSING: $var]";
			}
		);

		// The fallback should have been applied to missing variables
		$this->assertStringContainsString( "[MISSING: first_name]", $result );
		$this->assertStringContainsString( "[MISSING: last_name]", $result );
	}

	/**
	 * Test that fallback function receives the variable name without braces.
	 */
	public function test_fallback_receives_clean_variable_name(): void {
		$capturedVars = [];

		Templates::get(
			TemplateName::OWNER_REGISTRATION_NOTIFICATION,
			[
				"first_name" => "Test",
				"last_name"  => "User"
			], // Only provide first/last name, others will use fallback
			function ( string $var ) use ( &$capturedVars ): string {
				$capturedVars[] = $var;

				return "FALLBACK";
			}
		);

		// Verify that variable names don't contain braces
		foreach ( $capturedVars as $var ) {
			$this->assertStringNotContainsString( "{{", $var );
			$this->assertStringNotContainsString( "}}", $var );
		}
	}

	/**
	 * Test that partially provided variables are replaced correctly,
	 * with fallback used for missing ones.
	 */
	public function test_get_handles_partial_variable_replacement(): void {
		$result = Templates::get(
			TemplateName::OWNER_REGISTRATION_NOTIFICATION,
			[
				"first_name" => "John",
				"last_name"  => "Doe",
				"email"      => "john@example.com"
				// phone, class, registration_datetime missing
			],
			function ( string $var ): string {
				return "[PLACEHOLDER]";
			}
		);

		$this->assertStringContainsString( "John", $result );
		$this->assertStringContainsString( "Doe", $result );
		$this->assertStringContainsString( "john@example.com", $result );
		$this->assertStringContainsString( "[PLACEHOLDER]", $result );
	}

	/**
	 * Test that template variables with special characters are handled correctly.
	 */
	public function test_get_handles_special_characters_in_values(): void {
		$result = Templates::get(
			TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
			[
				"first_name" => "O'Brien",
				"last_name"  => "<test@example.com> & Co."
			]
		);

		$this->assertStringContainsString( "O'Brien", $result );
		$this->assertStringContainsString( "<test@example.com> & Co.", $result );
	}

	/**
	 * Test that empty string values for template variables work correctly.
	 */
	public function test_get_handles_empty_string_values(): void {
		$result = Templates::get(
			TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
			[
				"first_name" => "",
				"last_name"  => ""
			]
		);

		$this->assertIsString( $result );
		$this->assertStringNotContainsString( "{{first_name}}", $result );
		$this->assertStringNotContainsString( "{{last_name}}", $result );
	}

	/**
	 * Test that numeric values in template variables are converted to strings.
	 */
	public function test_get_handles_numeric_values(): void {
		$result = Templates::get(
			TemplateName::OWNER_REGISTRATION_NOTIFICATION,
			[
				"first_name"            => "Test",
				"last_name"             => "User",
				"email"                 => "test@example.com",
				"phone"                 => 5551234567, // numeric
				"class"                 => "Class 101", // string with numbers
				"registration_datetime" => "2026-02-01"
			]
		);

		$this->assertStringContainsString( "5551234567", $result );
		$this->assertStringContainsString( "Class 101", $result );
	}

	/**
	 * Test that the same variable can be used multiple times in a template
	 * and all instances are replaced.
	 */
	public function test_get_replaces_all_instances_of_repeated_variables(): void {
		$testFirstName = "UniqueFirstName123";
		$testLastName  = "UniqueLastName456";
		$result        = Templates::get(
			TemplateName::OWNER_REGISTRATION_NOTIFICATION,
			[
				"first_name"            => $testFirstName,
				"last_name"             => $testLastName,
				"email"                 => "test@example.com",
				"phone"                 => "555-0000",
				"class"                 => "Test Class",
				"registration_datetime" => "2026-02-01"
			]
		);

		// Count occurrences - the name variables may appear multiple times in owner template
		$firstNameOccurrences = substr_count( $result, $testFirstName );
		$lastNameOccurrences  = substr_count( $result, $testLastName );
		$this->assertGreaterThanOrEqual( 1, $firstNameOccurrences );
		$this->assertGreaterThanOrEqual( 1, $lastNameOccurrences );

		// Ensure no unreplaced instances remain
		$this->assertStringNotContainsString( "{{first_name}}", $result );
		$this->assertStringNotContainsString( "{{last_name}}", $result );
	}

	/**
	 * Test that whitespace in template variable values is preserved.
	 */
	public function test_get_preserves_whitespace_in_values(): void {
		$result = Templates::get(
			TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
			[
				"first_name" => "  First With  Spaces  ",
				"last_name"  => "  Last With  Spaces  "
			]
		);

		$this->assertStringContainsString( "  First With  Spaces  ", $result );
		$this->assertStringContainsString( "  Last With  Spaces  ", $result );
	}

	/**
	 * Test that template variable keys are case-sensitive.
	 */
	public function test_template_variable_keys_are_case_sensitive(): void {
		$result = Templates::get(
			TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
			[
				"FIRST_NAME" => "Wrong",     // Wrong case
				"first_name" => "Correct",   // Correct case
				"LAST_NAME"  => "Wrong",      // Wrong case
				"last_name"  => "Case"        // Correct case
			]
		);

		$this->assertStringContainsString( "Correct", $result );
		$this->assertStringContainsString( "Case", $result );
		$this->assertStringNotContainsString( "Wrong", $result );
	}

	/**
	 * Test that extra template variables (not in template) don't cause errors.
	 */
	public function test_get_ignores_extra_template_variables(): void {
		$result = Templates::get(
			TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
			[
				"first_name"  => "John",
				"last_name"   => "Doe",
				"extra_var_1" => "Should be ignored",
				"extra_var_2" => "Also ignored"
			]
		);

		$this->assertIsString( $result );
		$this->assertStringContainsString( "John", $result );
		$this->assertStringContainsString( "Doe", $result );
	}

	/**
	 * Test that both template types are accessible and return different content.
	 */
	public function test_different_templates_return_different_content(): void {
		$clientResult = Templates::get(
			TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
			[
				"first_name" => "Test",
				"last_name"  => "User"
			]
		);

		$ownerResult = Templates::get(
			TemplateName::OWNER_REGISTRATION_NOTIFICATION,
			[
				"first_name"            => "Test",
				"last_name"             => "User",
				"email"                 => "test@example.com",
				"phone"                 => "555-0000",
				"class"                 => "Test",
				"registration_datetime" => "2026-02-01"
			]
		);

		$this->assertNotEquals( $clientResult, $ownerResult );

		// Client template should mention "we will contact you"
		$this->assertStringContainsString( "contact you", $clientResult );

		// Owner template should be about new registration
		$this->assertStringContainsString( "registration", strtolower( $ownerResult ) );
	}

	/**
	 * Test that the returned template is valid HTML.
	 */
	public function test_get_returns_valid_html(): void {
		$result = Templates::get(
			TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
			[
				"first_name" => "Test",
				"last_name"  => "User"
			]
		);

		$this->assertStringContainsString( "<!DOCTYPE html>", $result );
		$this->assertStringContainsString( "<html", $result );
		$this->assertStringContainsString( "</html>", $result );
		$this->assertStringContainsString( "<body", $result );
		$this->assertStringContainsString( "</body>", $result );
	}

	/**
	 * Test that fallback function return value is properly used in the template.
	 */
	public function test_fallback_return_value_is_inserted_into_template(): void {
		$fallbackText = "CUSTOM_FALLBACK_12345";

		$result = Templates::get(
			TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
			[], // No variables, so fallback will be used
			function ( string $var ) use ( $fallbackText ): string {
				return $fallbackText;
			}
		);

		$this->assertStringContainsString( $fallbackText, $result );
	}

	/**
	 * Test that the template contains expected HTML structure and branding.
	 */
	public function test_template_contains_expected_branding(): void {
		$result = Templates::get(
			TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
			[
				"first_name" => "Test",
				"last_name"  => "User"
			]
		);

		$this->assertStringContainsString( "Go-Diva", $result );
		$this->assertStringContainsString( "Pole Dance", $result );
	}

	// ========================================================================
	// Tests for get_template_vars() method
	// ========================================================================

	/**
	 * Test that get_template_vars() returns an array.
	 */
	public function test_get_template_vars_returns_array(): void {
		$result = Templates::get_template_vars( TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION );

		$this->assertIsArray( $result );
	}

	/**
	 * Test that get_template_vars() returns template variables for customer template.
	 *
	 * Based on the specification, this function should parse the template
	 * and return all unique template variables found in it.
	 */
	public function test_get_template_vars_returns_customer_template_variables(): void {
		$result = Templates::get_template_vars( TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION );

		$this->assertIsArray( $result );
		$this->assertContains( "name", $result );
	}

	/**
	 * Test that get_template_vars() returns all template variables for owner template.
	 *
	 * The owner notification template contains multiple template variables
	 * that should all be returned.
	 */
	public function test_get_template_vars_returns_owner_template_variables(): void {
		$result = Templates::get_template_vars( TemplateName::OWNER_REGISTRATION_NOTIFICATION );

		$this->assertIsArray( $result );

		// Verify all expected template variables are present
		$this->assertContains( "name", $result );
		$this->assertContains( "class", $result );
		$this->assertContains( "registration_datetime", $result );
		$this->assertContains( "email", $result );
		$this->assertContains( "phone", $result );
	}

	/**
	 * Test that get_template_vars() returns unique values only (no duplicates).
	 *
	 * According to spec: "Only one of each template variable will be in that array"
	 * Even if a variable appears multiple times in the template, it should only
	 * appear once in the returned array.
	 */
	public function test_get_template_vars_returns_unique_values(): void {
		$result = Templates::get_template_vars( TemplateName::OWNER_REGISTRATION_NOTIFICATION );

		// Remove duplicates manually to compare
		$uniqueResult = array_unique( $result );

		// The count should be the same - meaning no duplicates
		$this->assertCount( count( $uniqueResult ), $result, "Result should contain only unique values" );
	}

	/**
	 * Test that different templates return different variable sets.
	 */
	public function test_get_template_vars_different_templates_return_different_vars(): void {
		$customerVars = Templates::get_template_vars( TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION );
		$ownerVars    = Templates::get_template_vars( TemplateName::OWNER_REGISTRATION_NOTIFICATION );

		$this->assertIsArray( $customerVars );
		$this->assertIsArray( $ownerVars );

		// Owner template should have more variables than customer template
		$this->assertGreaterThan( count( $customerVars ), count( $ownerVars ) );

		// The sets should be different
		$this->assertNotEquals( $customerVars, $ownerVars );
	}

	/**
	 * Test that get_template_vars() returns the exact number of unique variables.
	 *
	 * Customer template has 1 unique variable: name
	 * Owner template has 5 unique variables: name, class, registration_datetime, email, phone
	 */
	public function test_get_template_vars_returns_correct_count(): void {
		$customerVars = Templates::get_template_vars( TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION );
		$ownerVars    = Templates::get_template_vars( TemplateName::OWNER_REGISTRATION_NOTIFICATION );

		$this->assertCount( 1, $customerVars, "Customer template should have 1 template variable" );
		$this->assertCount( 5, $ownerVars, "Owner template should have 5 template variables" );
	}

	/**
	 * Test that get_template_vars() returns variable names without braces.
	 *
	 * Template variables are written as {{variable_name}}, but the function
	 * should return just the variable name without the {{ }} braces.
	 */
	public function test_get_template_vars_returns_names_without_braces(): void {
		$result = Templates::get_template_vars( TemplateName::OWNER_REGISTRATION_NOTIFICATION );

		foreach ( $result as $varName ) {
			$this->assertStringNotContainsString( "{{", $varName );
			$this->assertStringNotContainsString( "}}", $varName );
			$this->assertStringNotContainsString( "{", $varName );
			$this->assertStringNotContainsString( "}", $varName );
		}
	}

	/**
	 * Test that all returned variable names are non-empty strings.
	 */
	public function test_get_template_vars_returns_non_empty_strings(): void {
		$result = Templates::get_template_vars( TemplateName::OWNER_REGISTRATION_NOTIFICATION );

		$this->assertNotEmpty( $result, "Should return at least one variable" );

		foreach ( $result as $varName ) {
			$this->assertIsString( $varName );
			$this->assertNotEmpty( $varName );
		}
	}

	/**
	 * Test that variable names use expected format (lowercase with underscores).
	 *
	 * Based on the templates, variables follow snake_case naming convention.
	 */
	public function test_get_template_vars_returns_properly_formatted_names(): void {
		$result = Templates::get_template_vars( TemplateName::OWNER_REGISTRATION_NOTIFICATION );

		foreach ( $result as $varName ) {
			// Should be lowercase with underscores, no spaces
			$this->assertStringNotContainsString( " ", $varName, "Variable names should not contain spaces" );
			$this->assertEquals( strtolower( $varName ), $varName, "Variable names should be lowercase" );
		}
	}

	/**
	 * Test that the function works with both available template types.
	 *
	 * This ensures the function can handle all enum cases.
	 */
	public function test_get_template_vars_works_with_all_template_types(): void {
		// Should not throw exceptions for any template type
		$customerResult = Templates::get_template_vars( TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION );
		$ownerResult    = Templates::get_template_vars( TemplateName::OWNER_REGISTRATION_NOTIFICATION );

		$this->assertIsArray( $customerResult );
		$this->assertIsArray( $ownerResult );
	}

	/**
	 * Test that get_template_vars() returns all expected variables and no unexpected ones.
	 *
	 * This is a comprehensive test that verifies the exact set of variables.
	 */
	public function test_get_template_vars_returns_exact_variable_set(): void {
		$customerVars = Templates::get_template_vars( TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION );
		$ownerVars    = Templates::get_template_vars( TemplateName::OWNER_REGISTRATION_NOTIFICATION );

		// Expected variables for customer template
		$expectedCustomerVars = [ "name" ];
		sort( $expectedCustomerVars );

		// Expected variables for owner template
		$expectedOwnerVars = [ "name", "class", "registration_datetime", "email", "phone" ];
		sort( $expectedOwnerVars );

		// Sort actual results for comparison
		sort( $customerVars );
		sort( $ownerVars );

		$this->assertEquals( $expectedCustomerVars, $customerVars, "Customer template should have exactly the expected variables" );
		$this->assertEquals( $expectedOwnerVars, $ownerVars, "Owner template should have exactly the expected variables" );
	}
}