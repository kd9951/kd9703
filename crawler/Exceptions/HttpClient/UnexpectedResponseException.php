<?php

namespace Crawler\Exceptions\HttpClient;

use Crawler\Exceptions\HttpClientException;

/**
 * HTTPとしては200正常だが
 * 想定とは違うレスポンス
 */
class UnexpectedResponseException extends HttpClientException
{

}
