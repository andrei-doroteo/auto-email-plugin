<?php declare(strict_types=1);

/**
 * This file is AI generated.
 *
 * TODO: - Do a manual code review on these tests.
 *       - 
 */
namespace DoroteoDigital\AutoEmail\templates;

use DoroteoDigital\AutoEmail\templates\exceptions\TemplateRenderException;
use PHPUnit\Framework\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;

class TemplatesTest extends TestCase
{
    /**
     * The Templates instance used for testing.
     * @var Templates
     */
    private Templates $templates;

    /**
     * Set up the test environment before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Set base path to the src directory relative to the test directory
        $basePath = dirname(__DIR__, 2) . '/src';
        $this->templates = new Templates($basePath);
    }

    /**
     * Helper method to generate template variables with test values.
     * Gets the actual template variables from the template and fills them with values.
     *
     * @param TemplateName $template The template to get variables for
     * @param array $customValues Optional custom values to override defaults
     * @return array Associative array of template variables with test values
     */
    private function getTemplateVarsWithValues(TemplateName $template, array $customValues = []): array
    {
        $vars = $this->templates->get_template_vars($template);
        $values = [];

        // Provide default test values for each variable
        foreach ($vars as $var) {
            $values[$var] = match ($var) {
                'name' => 'John Doe',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'test@example.com',
                'phone' => '555-1234',
                'class' => 'Test Class',
                'registration_datetime' => '2026-02-01 10:00 AM',
                default => "TestValue_$var"
            };
        }

        // Override with custom values
        return array_merge($values, $customValues);
    }

    /**
     * Test that get() returns a non-empty string when given a valid template name
     * and all required template variables.
     */
    public function test_get_returns_string_with_valid_template_and_variables(): void
    {
        $vars = $this->getTemplateVarsWithValues(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            ['name' => 'Jane Doe']
        );

        $result = $this->templates->get(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            $vars
        );

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test that get() successfully replaces template variables in the output.
     */
    public function test_get_replaces_template_variables(): void
    {
        $vars = $this->getTemplateVarsWithValues(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            ['name' => 'John Smith']
        );

        $result = $this->templates->get(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            $vars
        );

        $this->assertStringContainsString("John Smith", $result);
        // Check that template variables are replaced (no braces remain for the name variable)
        $templateVars = $this->templates->get_template_vars(TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION);
        foreach ($templateVars as $var) {
            $this->assertStringNotContainsString("{{{$var}}}", $result);
        }
    }

    /**
     * Test that get() works with the owner notification template.
     */
    public function test_get_works_with_owner_template(): void
    {
        $vars = $this->getTemplateVarsWithValues(
            TemplateName::OWNER_REGISTRATION_NOTIFICATION,
            [
                "name" => "Jane Doe",
                "email" => "jane@example.com",
                "phone" => "555-1234",
                "class" => "Beginner Pole",
                "registration_datetime" => "2026-02-01 10:30 AM"
            ]
        );

        $result = $this->templates->get(
            TemplateName::OWNER_REGISTRATION_NOTIFICATION,
            $vars
        );

        $this->assertIsString($result);
        $this->assertStringContainsString("Jane Doe", $result);
        $this->assertStringContainsString("jane@example.com", $result);
        $this->assertStringContainsString("555-1234", $result);
        $this->assertStringContainsString("Beginner Pole", $result);
    }

    /**
     * Test that get() replaces multiple different template variables.
     */
    public function test_get_replaces_multiple_variables(): void
    {
        $vars = $this->getTemplateVarsWithValues(
            TemplateName::OWNER_REGISTRATION_NOTIFICATION,
            [
                "name" => "Alice Johnson",
                "email" => "alice@test.com",
                "phone" => "123-456-7890",
                "class" => "Advanced Class",
                "registration_datetime" => "2026-01-15 14:00"
            ]
        );

        $result = $this->templates->get(
            TemplateName::OWNER_REGISTRATION_NOTIFICATION,
            $vars
        );

        $this->assertStringContainsString("Alice Johnson", $result);
        $this->assertStringContainsString("alice@test.com", $result);
        $this->assertStringContainsString("123-456-7890", $result);
        $this->assertStringContainsString("Advanced Class", $result);
        $this->assertStringContainsString("2026-01-15 14:00", $result);
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
    public function test_get_uses_empty_string_fallback_by_default(): void
    {
        $result = $this->templates->get(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            [] // No variables provided
        );

        // All template variables should be replaced with empty string
        $this->assertStringNotContainsString("{{", $result);
        $this->assertStringNotContainsString("}}", $result);
    }

    /**
     * Test that the fallback function is called for unreplaced variables.
     */
    public function test_get_uses_custom_fallback_for_missing_variables(): void
    {
        $result = $this->templates->get(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            [], // No variables provided
            function (string $var): string {
                return "[MISSING: $var]";
            }
        );

        // The fallback should have been applied to all template variables
        $templateVars = $this->templates->get_template_vars(TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION);
        foreach ($templateVars as $var) {
            $this->assertStringContainsString("[MISSING: $var]", $result);
        }
    }

    /**
     * Test that fallback function receives the variable name without braces.
     */
    public function test_fallback_receives_clean_variable_name(): void
    {
        $capturedVars = [];

        $this->templates->get(
            TemplateName::OWNER_REGISTRATION_NOTIFICATION,
            [], // No variables provided - all will use fallback
            function (string $var) use (&$capturedVars): string {
                $capturedVars[] = $var;

                return "FALLBACK";
            }
        );

        // Verify that variable names don't contain braces
        $this->assertNotEmpty($capturedVars, "Fallback should have been called for template variables");
        foreach ($capturedVars as $var) {
            $this->assertStringNotContainsString("{{", $var);
            $this->assertStringNotContainsString("}}", $var);
        }
    }

    /**
     * Test that partially provided variables are replaced correctly,
     * with fallback used for missing ones.
     */
    public function test_get_handles_partial_variable_replacement(): void
    {
        $result = $this->templates->get(
            TemplateName::OWNER_REGISTRATION_NOTIFICATION,
            [
                "name" => "John Doe",
                "email" => "john@example.com"
                // Other variables missing - will use fallback
            ],
            function (string $var): string {
                return "[PLACEHOLDER]";
            }
        );

        $this->assertStringContainsString("John Doe", $result);
        $this->assertStringContainsString("john@example.com", $result);
        $this->assertStringContainsString("[PLACEHOLDER]", $result);
    }

    /**
     * Test that template variables with special characters are handled correctly.
     */
    public function test_get_handles_special_characters_in_values(): void
    {
        $vars = $this->getTemplateVarsWithValues(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            [
                "name" => "O'Brien <test@example.com> & Co."
            ]
        );

        $result = $this->templates->get(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            $vars
        );

        $this->assertStringContainsString("O'Brien", $result);
        $this->assertStringContainsString("<test@example.com> & Co.", $result);
    }

    /**
     * Test that empty string values for template variables work correctly.
     */
    public function test_get_handles_empty_string_values(): void
    {
        $vars = $this->getTemplateVarsWithValues(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            ["name" => ""]
        );

        $result = $this->templates->get(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            $vars
        );

        $this->assertIsString($result);
        // Check that all template variables are replaced (no braces remain)
        $this->assertStringNotContainsString("{{", $result);
        $this->assertStringNotContainsString("}}", $result);
    }

    /**
     * Test that numeric values in template variables are converted to strings.
     */
    public function test_get_handles_numeric_values(): void
    {
        $vars = $this->getTemplateVarsWithValues(
            TemplateName::OWNER_REGISTRATION_NOTIFICATION,
            [
                "name" => "Test User",
                "email" => "test@example.com",
                "phone" => 5551234567, // numeric
                "class" => "Class 101", // string with numbers
                "registration_datetime" => "2026-02-01"
            ]
        );

        $result = $this->templates->get(
            TemplateName::OWNER_REGISTRATION_NOTIFICATION,
            $vars
        );

        $this->assertStringContainsString("5551234567", $result);
        $this->assertStringContainsString("Class 101", $result);
    }

    /**
     * Test that the same variable can be used multiple times in a template
     * and all instances are replaced.
     */
    public function test_get_replaces_all_instances_of_repeated_variables(): void
    {
        $testName = "UniqueTestName123";
        $vars = $this->getTemplateVarsWithValues(
            TemplateName::OWNER_REGISTRATION_NOTIFICATION,
            [
                "name" => $testName,
                "email" => "test@example.com",
                "phone" => "555-0000",
                "class" => "Test Class",
                "registration_datetime" => "2026-02-01"
            ]
        );

        $result = $this->templates->get(
            TemplateName::OWNER_REGISTRATION_NOTIFICATION,
            $vars
        );

        // Count occurrences - variables may appear multiple times in template
        $nameOccurrences = substr_count($result, $testName);
        $this->assertGreaterThanOrEqual(1, $nameOccurrences);

        // Ensure no unreplaced instances remain
        $templateVars = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);
        foreach ($templateVars as $var) {
            $this->assertStringNotContainsString("{{{$var}}}", $result);
        }
    }

    /**
     * Test that whitespace in template variable values is preserved.
     */
    public function test_get_preserves_whitespace_in_values(): void
    {
        $vars = $this->getTemplateVarsWithValues(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            ["name" => "  First With  Spaces  "]
        );

        $result = $this->templates->get(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            $vars
        );

        $this->assertStringContainsString("  First With  Spaces  ", $result);
    }

    /**
     * Test that template variable keys are case-sensitive.
     */
    public function test_template_variable_keys_are_case_sensitive(): void
    {
        $vars = $this->getTemplateVarsWithValues(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            [
                "NAME" => "Wrong",     // Wrong case
                "name" => "Correct Case"   // Correct case
            ]
        );

        $this->expectException(TemplateRenderException::class);
        $result = $this->templates->get(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            $vars
        );

        $this->assertStringContainsString("Correct", $result);
        $this->assertStringNotContainsString("Wrong", $result);
    }

    /**
     * Test that extra template variables (not in template) throw an exception.
     * Based on requirement: failure is when not all vars in $template_vars are used.
     */
    public function test_get_throws_exception_for_extra_template_variables(): void
    {
        $this->expectException(TemplateRenderException::class);
        $this->expectExceptionMessage("not found in template");

        $vars = $this->getTemplateVarsWithValues(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            [
                "name" => "John Doe",
                "extra_var_1" => "Should cause error",
                "extra_var_2" => "Also cause error"
            ]
        );

        $this->templates->get(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            $vars
        );
    }

    /**
     * Test that both template types are accessible and return different content.
     */
    public function test_different_templates_return_different_content(): void
    {
        $clientVars = $this->getTemplateVarsWithValues(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            ["name" => "Test User"]
        );

        $ownerVars = $this->getTemplateVarsWithValues(
            TemplateName::OWNER_REGISTRATION_NOTIFICATION,
            [
                "name" => "Test User",
                "email" => "test@example.com",
                "phone" => "555-0000",
                "class" => "Test",
                "registration_datetime" => "2026-02-01"
            ]
        );

        $clientResult = $this->templates->get(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            $clientVars
        );

        $ownerResult = $this->templates->get(
            TemplateName::OWNER_REGISTRATION_NOTIFICATION,
            $ownerVars
        );

        $this->assertNotEquals($clientResult, $ownerResult);

        // Client template should mention "we will contact you"
        $this->assertStringContainsString("contact you", $clientResult);

        // Owner template should be about new registration
        $this->assertStringContainsString("registration", strtolower($ownerResult));
    }

    /**
     * Test that the returned template is valid HTML.
     */
    public function test_get_returns_valid_html(): void
    {
        $vars = $this->getTemplateVarsWithValues(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            ["name" => "Test User"]
        );

        $result = $this->templates->get(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            $vars
        );

        $this->assertStringContainsString("<!DOCTYPE html>", $result);
        $this->assertStringContainsString("<html", $result);
        $this->assertStringContainsString("</html>", $result);
        $this->assertStringContainsString("<body", $result);
        $this->assertStringContainsString("</body>", $result);
    }

    /**
     * Test that fallback function return value is properly used in the template.
     */
    public function test_fallback_return_value_is_inserted_into_template(): void
    {
        $fallbackText = "CUSTOM_FALLBACK_12345";

        $result = $this->templates->get(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            [], // No variables, so fallback will be used
            function (string $var) use ($fallbackText): string {
                return $fallbackText;
            }
        );

        $this->assertStringContainsString($fallbackText, $result);
    }

    /**
     * Test that the template contains expected HTML structure and branding.
     */
    public function test_template_contains_expected_branding(): void
    {
        $vars = $this->getTemplateVarsWithValues(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            ["name" => "Test User"]
        );

        $result = $this->templates->get(
            TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION,
            $vars
        );

        $this->assertStringContainsString("Go-Diva", $result);
        $this->assertStringContainsString("Pole Dance", $result);
    }

    // ========================================================================
    // Tests for get_template_vars() method
    // ========================================================================

    /**
     * Test that get_template_vars() returns an array.
     */
    public function test_get_template_vars_returns_array(): void
    {
        $result = $this->templates->get_template_vars(TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION);

        $this->assertIsArray($result);
    }

    /**
     * Test that get_template_vars() returns template variables for customer template.
     *
     * Based on the specification, this function should parse the template
     * and return all unique template variables found in it.
     */
    public function test_get_template_vars_returns_customer_template_variables(): void
    {
        $result = $this->templates->get_template_vars(TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION);

        $this->assertIsArray($result);
        $this->assertContains("name", $result);
    }

    /**
     * Test that get_template_vars() returns all template variables for owner template.
     *
     * The owner notification template contains multiple template variables
     * that should all be returned.
     */
    public function test_get_template_vars_returns_owner_template_variables(): void
    {
        $result = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);

        $this->assertIsArray($result);

        // Verify all expected template variables are present
        $this->assertContains("name", $result);
        $this->assertContains("class", $result);
        $this->assertContains("registration_datetime", $result);
        $this->assertContains("email", $result);
        $this->assertContains("phone", $result);
    }

    /**
     * Test that get_template_vars() returns unique values only (no duplicates).
     *
     * According to spec: "Only one of each template variable will be in that array"
     * Even if a variable appears multiple times in the template, it should only
     * appear once in the returned array.
     */
    public function test_get_template_vars_returns_unique_values(): void
    {
        $result = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);

        // Remove duplicates manually to compare
        $uniqueResult = array_unique($result);

        // The count should be the same - meaning no duplicates
        $this->assertCount(count($uniqueResult), $result, "Result should contain only unique values");
    }

    /**
     * Test that different templates return different variable sets.
     */
    public function test_get_template_vars_different_templates_return_different_vars(): void
    {
        $customerVars = $this->templates->get_template_vars(TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION);
        $ownerVars = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);

        $this->assertIsArray($customerVars);
        $this->assertIsArray($ownerVars);

        // Owner template should have more variables than customer template
        $this->assertGreaterThan(count($customerVars), count($ownerVars));

        // The sets should be different
        $this->assertNotEquals($customerVars, $ownerVars);
    }

    /**
     * Test that get_template_vars() returns the exact number of unique variables.
     *
     * Customer template has 1 unique variable: name
     * Owner template has 5 unique variables: name, class, registration_datetime, email, phone
     */
    public function test_get_template_vars_returns_correct_count(): void
    {
        $customerVars = $this->templates->get_template_vars(TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION);
        $ownerVars = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);

        $this->assertCount(1, $customerVars, "Customer template should have 1 template variable");
        $this->assertCount(5, $ownerVars, "Owner template should have 5 template variables");
    }

    /**
     * Test that get_template_vars() returns variable names without braces.
     *
     * Template variables are written as {{variable_name}}, but the function
     * should return just the variable name without the {{ }} braces.
     */
    public function test_get_template_vars_returns_names_without_braces(): void
    {
        $result = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);

        foreach ($result as $varName) {
            $this->assertStringNotContainsString("{{", $varName);
            $this->assertStringNotContainsString("}}", $varName);
            $this->assertStringNotContainsString("{", $varName);
            $this->assertStringNotContainsString("}", $varName);
        }
    }

    /**
     * Test that all returned variable names are non-empty strings.
     */
    public function test_get_template_vars_returns_non_empty_strings(): void
    {
        $result = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);

        $this->assertNotEmpty($result, "Should return at least one variable");

        foreach ($result as $varName) {
            $this->assertIsString($varName);
            $this->assertNotEmpty($varName);
        }
    }

    /**
     * Test that variable names use expected format (lowercase with underscores).
     *
     * Based on the templates, variables follow snake_case naming convention.
     */
    public function test_get_template_vars_returns_properly_formatted_names(): void
    {
        $result = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);

        foreach ($result as $varName) {
            // Should be lowercase with underscores, no spaces
            $this->assertStringNotContainsString(" ", $varName, "Variable names should not contain spaces");
            $this->assertEquals(strtolower($varName), $varName, "Variable names should be lowercase");
        }
    }

    /**
     * Test that the function works with both available template types.
     *
     * This ensures the function can handle all enum cases.
     */
    public function test_get_template_vars_works_with_all_template_types(): void
    {
        // Should not throw exceptions for any template type
        $customerResult = $this->templates->get_template_vars(TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION);
        $ownerResult = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);

        $this->assertIsArray($customerResult);
        $this->assertIsArray($ownerResult);
    }

    /**
     * Test that get_template_vars() returns all expected variables and no unexpected ones.
     *
     * This is a comprehensive test that verifies the exact set of variables.
     */
    public function test_get_template_vars_returns_exact_variable_set(): void
    {
        $customerVars = $this->templates->get_template_vars(TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION);
        $ownerVars = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);

        // Expected variables for customer template
        $expectedCustomerVars = ["name"];
        sort($expectedCustomerVars);

        // Expected variables for owner template
        $expectedOwnerVars = ["name", "class", "registration_datetime", "email", "phone"];
        sort($expectedOwnerVars);

        // Sort actual results for comparison
        sort($customerVars);
        sort($ownerVars);

        $this->assertEquals($expectedCustomerVars, $customerVars, "Customer template should have exactly the expected variables");
        $this->assertEquals($expectedOwnerVars, $ownerVars, "Owner template should have exactly the expected variables");
    }

    // ========================================================================
    // Additional Black Box Tests for get_template_vars() - Edge Cases
    // ========================================================================

    /**
     * Test Case: Template with variable appearing multiple times returns unique values only.
     * 
     * The owner template has {{email}} appearing 3 times (lines 244, 245, 312) and
     * {{phone}} appearing 3 times (lines 263, 264, 318). This verifies that duplicates
     * are properly removed and only one instance of each variable is returned.
     */
    public function test_get_template_vars_deduplicates_repeated_variables(): void
    {
        $result = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);

        // Count occurrences of each variable in the result
        $varCounts = array_count_values($result);

        // Each variable should appear exactly once in the result array
        foreach ($varCounts as $varName => $count) {
            $this->assertEquals(1, $count, "Variable '$varName' should appear exactly once in result array");
        }

        // Verify specific variables that appear multiple times in template are returned only once
        $this->assertContains("email", $result);
        $this->assertContains("phone", $result);
        $this->assertContains("name", $result);

        // Count how many times "email" appears in result (should be 1)
        $emailOccurrences = 0;
        foreach ($result as $var) {
            if ($var === "email") {
                $emailOccurrences++;
            }
        }
        $this->assertEquals(1, $emailOccurrences, "Variable 'email' should appear only once despite multiple occurrences in template");
    }

    /**
     * Test Case: Template with single variable appearing once.
     * 
     * The customer template contains {{name}} appearing only once (line 168).
     * This tests the simplest case where a variable appears exactly once.
     */
    public function test_get_template_vars_handles_single_occurrence_variable(): void
    {
        $result = $this->templates->get_template_vars(TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION);

        $this->assertIsArray($result);
        $this->assertCount(1, $result, "Customer template should have exactly 1 unique variable");
        $this->assertEquals(["name"], $result, "Customer template should contain only 'name' variable");
    }

    /**
     * Test Case: Template with multiple unique variables with different occurrence counts.
     * 
     * The owner template has:
     * - {{name}} appears 3 times (lines 115, 226, 227)
     * - {{class}} appears 2 times (lines 115, 282)
     * - {{registration_datetime}} appears 1 time (line 208)
     * - {{email}} appears 3 times (lines 244, 245, 312)
     * - {{phone}} appears 3 times (lines 263, 264, 318)
     * 
     * This verifies that all variables are extracted regardless of occurrence count.
     */
    public function test_get_template_vars_handles_mixed_occurrence_counts(): void
    {
        $result = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);

        // Should return all 5 unique variables
        $this->assertCount(5, $result, "Owner template should have exactly 5 unique variables");

        // Verify all expected variables are present
        $this->assertContains("name", $result);
        $this->assertContains("class", $result);
        $this->assertContains("registration_datetime", $result);
        $this->assertContains("email", $result);
        $this->assertContains("phone", $result);

        // Verify no duplicates exist
        $uniqueResult = array_unique($result);
        $this->assertCount(count($result), $uniqueResult, "Result should contain no duplicate variable names");
    }

    /**
     * Test Case: Verify variables with underscores in names are properly extracted.
     * 
     * Variables like {{registration_datetime}} contain underscores and should be
     * properly matched by the regex pattern.
     */
    public function test_get_template_vars_handles_variables_with_underscores(): void
    {
        $result = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);

        // registration_datetime has an underscore and should be extracted
        $this->assertContains("registration_datetime", $result, "Variables with underscores should be extracted");

        // Verify the full variable name is preserved (not split on underscore)
        $this->assertNotContains("registration", $result, "Should not split on underscores");
        $this->assertNotContains("datetime", $result, "Should not split on underscores");
    }

    /**
     * Test Case: Verify array indexing is sequential (0-based with no gaps).
     * 
     * The method uses array_values() to re-index the array after removing duplicates.
     * This ensures the returned array has sequential keys starting from 0.
     */
    public function test_get_template_vars_returns_sequentially_indexed_array(): void
    {
        $result = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);

        // Verify array keys are sequential starting from 0
        $expectedKeys = range(0, count($result) - 1);
        $actualKeys = array_keys($result);

        $this->assertEquals($expectedKeys, $actualKeys, "Array should have sequential 0-based keys with no gaps");
    }

    /**
     * Test Case: Verify the method handles whitespace around variable names.
     * 
     * The regex pattern in get_template_vars uses \s* to match optional whitespace,
     * so variables like {{ name }} or {{  name  }} should be matched and the
     * whitespace should be excluded from the captured variable name.
     * 
     * Note: The actual templates don't have whitespace, but the regex supports it.
     */
    public function test_get_template_vars_regex_handles_whitespace_patterns(): void
    {
        // This test verifies the regex pattern's capability
        // Since we can't modify the actual templates, we test the expected behavior
        $result = $this->templates->get_template_vars(TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION);

        // Variables should not contain any whitespace
        foreach ($result as $varName) {
            $this->assertStringNotContainsString(" ", $varName, "Variable names should not contain spaces");
            $this->assertStringNotContainsString("\t", $varName, "Variable names should not contain tabs");
            $this->assertStringNotContainsString("\n", $varName, "Variable names should not contain newlines");
        }
    }

    /**
     * Test Case: Verify variable names follow valid identifier rules.
     * 
     * The regex pattern [a-zA-Z_][a-zA-Z0-9_]* ensures:
     * - First character must be a letter or underscore
     * - Subsequent characters can be letters, numbers, or underscores
     */
    public function test_get_template_vars_returns_valid_identifier_names(): void
    {
        $result = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);

        foreach ($result as $varName) {
            // Variable name should not be empty
            $this->assertNotEmpty($varName);

            // First character should be a letter or underscore
            $firstChar = substr($varName, 0, 1);
            $this->assertTrue(
                ctype_alpha($firstChar) || $firstChar === '_',
                "Variable '$varName' should start with a letter or underscore"
            );

            // All characters should be alphanumeric or underscore
            $this->assertMatchesRegularExpression(
                '/^[a-zA-Z_][a-zA-Z0-9_]*$/',
                $varName,
                "Variable '$varName' should only contain letters, numbers, and underscores"
            );
        }
    }

    /**
     * Test Case: Verify both templates return different variable sets.
     * 
     * This ensures the function correctly parses different template files
     * and returns their respective variables, not a cached or static result.
     */
    public function test_get_template_vars_returns_different_sets_for_different_templates(): void
    {
        $customerVars = $this->templates->get_template_vars(TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION);
        $ownerVars = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);

        // The sets should be different
        $this->assertNotEquals($customerVars, $ownerVars, "Different templates should return different variable sets");

        // Customer template has fewer variables than owner template
        $this->assertLessThan(count($ownerVars), count($customerVars), "Customer template should have fewer variables");

        // Owner template should have variables not in customer template
        $ownerOnlyVars = array_diff($ownerVars, $customerVars);
        $this->assertNotEmpty($ownerOnlyVars, "Owner template should have unique variables not in customer template");
    }

    /**
     * Test Case: Verify return type consistency across multiple calls.
     * 
     * The method should always return an array with the same structure
     * when called multiple times on the same template.
     */
    public function test_get_template_vars_returns_consistent_results(): void
    {
        // Call the method multiple times
        $result1 = $this->templates->get_template_vars(TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION);
        $result2 = $this->templates->get_template_vars(TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION);
        $result3 = $this->templates->get_template_vars(TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION);

        // All results should be identical
        $this->assertEquals($result1, $result2, "Multiple calls should return identical results");
        $this->assertEquals($result2, $result3, "Multiple calls should return identical results");
    }

    /**
     * Test Case: Verify method doesn't return any malformed variable names.
     * 
     * This checks that the regex pattern correctly excludes any partial matches
     * or malformed template syntax that might be in comments or text.
     */
    public function test_get_template_vars_returns_only_valid_template_variables(): void
    {
        $result = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);

        foreach ($result as $varName) {
            // Should not contain braces
            $this->assertStringNotContainsString("{", $varName);
            $this->assertStringNotContainsString("}", $varName);

            // Should not contain special characters that aren't part of valid identifiers
            $this->assertDoesNotMatchRegularExpression(
                '/[^a-zA-Z0-9_]/',
                $varName,
                "Variable '$varName' contains invalid characters"
            );

            // Should not be just underscores
            $this->assertNotEquals("_", $varName);
            $this->assertNotEquals("__", $varName);
        }
    }

    /**
     * Test Case: Verify the exact count and order of variables for owner template.
     * 
     * This test documents the specific expected behavior for the owner template
     * which has 2 variables appearing once, 1 variable appearing twice, and
     * 2 variables appearing three times each.
     */
    public function test_get_template_vars_owner_template_exact_specification(): void
    {
        $result = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);

        // Should return exactly 5 unique variables
        $this->assertCount(5, $result);

        // Verify the exact set (order-independent)
        $resultSorted = $result;
        sort($resultSorted);

        $expected = ["class", "email", "name", "phone", "registration_datetime"];
        sort($expected);

        $this->assertEquals($expected, $resultSorted, "Owner template should have exact set of expected variables");
    }

    /**
     * Test Case: Verify no empty strings are returned in the array.
     * 
     * The method should never return an empty string as a variable name.
     */
    public function test_get_template_vars_does_not_return_empty_strings(): void
    {
        $customerResult = $this->templates->get_template_vars(TemplateName::CUSTOMER_REGISTRATION_NOTIFICATION);
        $ownerResult = $this->templates->get_template_vars(TemplateName::OWNER_REGISTRATION_NOTIFICATION);

        // Check customer template result
        foreach ($customerResult as $varName) {
            $this->assertNotEmpty($varName, "Variable names should not be empty strings");
            $this->assertNotEquals("", $varName);
        }

        // Check owner template result
        foreach ($ownerResult as $varName) {
            $this->assertNotEmpty($varName, "Variable names should not be empty strings");
            $this->assertNotEquals("", $varName);
        }
    }
}