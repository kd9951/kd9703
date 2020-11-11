<?php
namespace Kd9703\Constants\Follow;

use Kd9703\Constants\Enum;

class FollowedBackType extends Enum
{
    const MANUAL  = 1;
    const AUTO    = 2;
    const ORGANIC = 9;

    const LIST = [
        self::MANUAL,
        self::AUTO,
        self::ORGANIC,
    ];
}
