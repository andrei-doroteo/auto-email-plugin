<?php declare(strict_types=1);

use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;
use DoroteoDigital\AutoEmail\parser\Parser;
use DoroteoDigital\AutoEmail\exceptions\MissingTemplateVariableException;
use \PHPUnit\Framework\Attributes\DataProvider;

final class ParserTest extends TestCase
{
    private Parser $parser;

    protected function setUp(): void
    {
        $this->parser = new Parser();
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
    public function testParseOneStringEmptyArray(string $template_var): void
    {
        $this->expectException(MissingTemplateVariableException::class);
        $this->parser->parse($template_var, []);
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
}
