<?php

namespace Crawler\Exceptions\HttpClient;

use Crawler\Exceptions\HttpClientException;

/**
 * アクセスできない
 * 権限がないとかロックされているとか
 * 一時的なものかもしれない…
 */
class AccessDeniedException extends HttpClientException
{

}
