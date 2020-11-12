<?php

namespace Kd9703\Usecases;

use Crawler\Support\Random;
use Crawler\Support\Timer;
use Kd9703\Framework\StrictInvokator\StrictInvokator;
use Kd9703\Logger\Interfaces\OwnerLogger;
use Kd9703\Logger\Interfaces\SystemLogger;

class Usecase
{
    use StrictInvokator;

    /**
     * @var array
     */
    protected $resources = [];
    /**
     * @var array
     */
    protected $mediaAccesses = [];
    /**
     * @var array
     */
    protected $usecases = [];
    /**
     * @var Random
     */
    protected $random = [];
    /**
     * @var Timer
     */
    protected $timer = [];
    /**
     * @var SystemLogger
     */
    protected $systemLogger = [];
    /**
     * @var OwnerLogger
     */
    protected $ownerLogger = [];

    /**
     * 依存オブジェクトを受け取る
     */
    public function __construct(
        Random $random,
        Timer $timer,
        SystemLogger $systemLogger,
        OwnerLogger $ownerLogger
    ) {
        $this->random       = $random;
        $this->timer        = $timer;
        $this->systemLogger = $systemLogger;
        $this->ownerLogger  = $ownerLogger;
    }

}
