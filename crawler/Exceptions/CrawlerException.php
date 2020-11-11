<?php
namespace Crawler\Exceptions;

/**
 * CrawlerException
 *   <>--- HttpClientException
 *     <>--- HttpClient\AuthentificationException
 *     <>--- HttpClient\NotFoundException
 *     <>--- HttpClient\AccessDeniedException
 *     <>--- HttpClient\OtherException
 *   <>--- ParserException
 *     <>--- Parser\PatternNotMatchedException
 *     <>--- Parser\ValidationException
 */
abstract class CrawlerException extends \RuntimeException
{

}
