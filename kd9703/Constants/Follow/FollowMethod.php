<?php
namespace Kd9703\Constants\Follow;

use Kd9703\Constants\Enum;

class FollowMethod extends Enum
{
    const MANUAL = 1;
    const AUTO   = 2;

    const LIST = [
        self::MANUAL,
        self::AUTO,
    ];
}
