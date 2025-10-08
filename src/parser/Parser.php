<?php declare(strict_types=1);

namespace DoroteoDigital\AutoEmail\parser;

class Parser
{


    /*
     *
     */
    function __construct()
    {

    }

    /*
     * Goes through $file line by line and replaces anything in the form {{template_variable}}
     * with respective value in $vars if any.
     * Template variable replacing starts after first curly bracket and ends after at last curly bracket.
     * Any whitespace inside the first and last curly brackets are ignored. Template variable names cannot have spaces.
     * if there is no value to replace with in $vars, throws MissingTemplateVariableException.
     */
    public function parse(string $file, array $vars): string
    {
        // TODO
        /**
         * Draft implementation: preg_replace('/{\s?{\s?\w*\s?}\s?}/', "Replacement", $file)
         */
        return ""; // stub
    }

}
