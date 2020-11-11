<?php
namespace Kd9703\Constants;

class Sex extends Enum
{
    const FEMALE    = 1;
    const MALE      = 2;
    const UNDEFINED = 9;

    const LIST = [
        self::FEMALE,
        self::MALE,
        self::UNDEFINED,
    ];
}
