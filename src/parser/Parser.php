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

    /**
     * Parses template variables in $file and replaces them with provided values in $vars.
     * 
     * Matches template variables in the form {{variable_name}}, in $file and replaces each
     * with the corresponding value from $vars if any.
     *
     * A template variable begins at the first opening curly bracket and ends after the second closing curly bracket.
     * Any whitespace within the first and last curly brackets are ignored. Template variable names cannot have spaces
     * and must be at least one character long.
     * 
     * If there is no key in $vars with a value to replace the template variable, that unset key is appended to $unset
     * and the template variable in $file is unchanged.
     * 
     * Template Variable Examples:
     *  - {{my_name}}
     *  - {{ my_name }}
     *  - { {my_name} }
     *  - { { my_name } }
     *  - {{ my_name} }
     *
     * @param string $file The string that gets replaced.
     * @param array $vars An associative array of template variable names and their values for replacement.
     * @param array $unset An optional array, passed by reference, that will be overwritten and populated with all unset vars.
     *
     * @return string The parsed version of $file.
     */
    public function parse(string $file, array $vars, array &$unset = []): string
    {

        return preg_replace_callback("/{\s?{\s?\w+\s?}\s?}/",
            function ($matches) use ($vars, &$unset) {

                $match = $matches[0];
                $label = trim($match, " \n\r\t\v\x00{}");

                if (!isset($vars[$label])) {

                    if (!in_array($label, $unset, true)) $unset[] = $label;
                    return $match;
                }
                return $vars[$label];
            },
            $file);

    }
}
