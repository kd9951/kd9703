<?php
namespace Kd9703\Constants\Job;

use Kd9703\Constants\Enum;

class Status extends Enum
{
    const STANDBY = 0;
    const RUNNING = 1;
    const CLOSED  = 9;

    const LIST = [
        self::STANDBY,
        self::RUNNING,
        self::CLOSED,
    ];
}
