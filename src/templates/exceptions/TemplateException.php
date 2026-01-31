<?php declare(strict_types=1);


namespace DoroteoDigital\AutoEmail\templates\exceptions;
use \Exception;

/**
 * Base exception for all template-related errors.
 * Catching this will catch all template exceptions.
 */
class TemplateException extends Exception
{
}

/**
 * Thrown when requested template name doesn't exist.
 */
class TemplateNotFoundException extends TemplateException
{
}

/**
 * Thrown when template file cannot be accessed or read.
 */
class TemplateFileException extends TemplateException
{
}

/**
 * Thrown when template variable replacement fails.
 */
class TemplateRenderException extends TemplateException
{
}