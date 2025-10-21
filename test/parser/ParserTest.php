<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use DoroteoDigital\AutoEmail\parser\Parser;
use \PHPUnit\Framework\Attributes\DataProvider;

final class ParserTest extends TestCase
{
    private Parser $parser;

    protected function setUp(): void
    {
        $this->parser = new Parser();
        $this->test_email = file_get_contents(__DIR__ . "/templates/testEmail__input");
    }

    public static function singleTemplateVariableProvider(): array
    {
        return [
            ['{{var}}'],
            ['{{ var }}'],
            ['{ { var } }'],
            ['{ { var}}'],
            ['{ {var}}'],
            ['{{var } }'],
            ['{{var} }']
        ];
    }

    // Test the case the string to parse is empty and there are no vars for replacement
    public function testParseEmptyStringEmptyArray(): void
    {
        $this->assertSame("", $this->parser->parse("", []));
    }

    #[DataProvider('singleTemplateVariableProvider')]
    public function testParseOneStringValidArray(string $template_var): void
    {

        $parsed = $this->parser->parse($template_var, ['var' => "Hello World!"]);
        $this->assertSame("Hello World!", $parsed);
    }

    #[DataProvider('singleTemplateVariableProvider')]
    public function testParseOneStringWithContentBeforeAndAfterValidArray(string $template_var): void
    {

        $parsed = $this->parser->parse("Before " . $template_var . " After", ['var' => "Hello World!"]);
        $this->assertSame("Before Hello World! After", $parsed);
    }

    public function testParseHtmlAllSet(): void
    {
        $expected = file_get_contents(__DIR__ . "/templates/testEmail__expected");

        $actual = $this->parser->parse($this->test_email, [
            'name' => 'John',
            'date' => 'January 1, 1999',
            'username' => 'john123',
            'email' => 'john_doe123@domain.com',
            'account_type' => 'Premium',
            'verification_code' => '12345678',
            'company_name' => 'Company',
            'year' => '1999',
        ]);

        $this->assertSame($expected, $actual);
    }

    public function testParseHtmlAllUnset(): void
    {

        $unset = [];
        $actual = $this->parser->parse($this->test_email, [],
            $unset);

        $this->assertSame($this->test_email, $actual);
        $this->assertSame([
            'name',
            'date',
            'username',
            'email',
            'account_type',
            'verification_code',
            'company_name',
            'year',
        ], $unset);
    }

    public function testParseHtmlMixedUnset(): void
    {

        $unset = [];
        $expected = file_get_contents(__DIR__ . "/templates/testEmail__expected_mixed_unset");
        $actual = $this->parser->parse($this->test_email, ['name' => 'John'],
            $unset);

        $this->assertSame($expected, $actual);
        $this->assertSame([
            'date',
            'username',
            'email',
            'account_type',
            'verification_code',
            'company_name',
            'year',
        ], $unset);
    }

}
