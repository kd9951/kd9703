<?php
namespace Kd9703\MediaAccess\Twitter;

use Crawler\HttpClientInterface;
use Crawler\ParserInterface;
use Crawler\Support\Random;
use Crawler\Support\Wait;
use Kd9703\Framework\StrictInvokator\StrictInvokator;
use Kd9703\Logger\Interfaces\OwnerLogger;
use Kd9703\Logger\Interfaces\SystemLogger;

/**
 * 通知を取得
 */
abstract class MediaAccess
{
    use StrictInvokator;

    const ENDPOINT_TOP    = 'https://twitter.jp/';
    const ENDPOINT_LOGIN  = 'https://twitter.jp/login';
    const ENDPOINT_MYPAGE = 'https://twitter.jp/my/'; // :myid

    /**
     * @var array
     */
    protected $mediaAccesses = [];

    /**
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @var ParserInterface
     */
    protected $parser;

    /**
     * @var SystemLogger
     */
    protected $system_logger;

    /**
     * @var OwnerLogger
     */
    protected $owner_logger;

    /**
     * @var Wait
     */
    protected $wait;

    /**
     * @var Random
     */
    protected $random;

    /**
     * Undocumented function
     *
     * @param HttpClientInterface $client
     * @param ParserInterface     $parser
     */
    public function __construct(
        HttpClientInterface $client,
        ParserInterface $parser,
        SystemLogger $system_logger,
        OwnerLogger $owner_logger,
        Wait $wait,
        Random $random
    ) {
        $this->client        = $client;
        $this->parser        = $parser;
        $this->system_logger = $system_logger;
        $this->owner_logger  = $owner_logger;
        $this->wait          = $wait;
        $this->random        = $random;

        $this->client->setUserAgent('pc.chrome');
    }

}
