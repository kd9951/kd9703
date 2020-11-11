<?php
namespace Kd9703\Constants\Notice;

use Kd9703\Constants\Enum;

class NoticeType extends Enum
{
    const LIKED_POST    = 1;
    const LIKED_COMMENT = 2;
    const FOLLOWED      = 3;
    const COMMENTED     = 4;
    const REPLIED       = 5;
    const OTHER         = 8;
    const AD            = 9;

    const LIST = [
        self::LIKED_POST,
        self::LIKED_COMMENT,
        self::FOLLOWED,
        self::COMMENTED,
        self::REPLIED,
        self::OTHER,
        self::AD,
    ];
}
