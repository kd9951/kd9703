<?php
namespace Kd9703\Constants;

/**
 * ISO-8601 曜日番号
 * date('N')
 */
class Weekday extends Enum
{
    const MONDAY    = 1; // 月曜日
    const TUESDAY   = 2; // 火曜日
    const WEDNESDAY = 3; // 水曜日
    const THURSDAY  = 4; // 木曜日
    const FRIDAY    = 5; // 金曜日
    const SATURNDAY = 6; // 土曜日
    const SUNDAY    = 7; // 日曜日

    const LIST = [
        self::MONDAY,
        self::TUESDAY,
        self::WEDNESDAY,
        self::THURSDAY,
        self::FRIDAY,
        self::SATURNDAY,
        self::SUNDAY,
    ];

    // date('w')
    const PHP_WEEK = [
        0 => self::SUNDAY,
        1 => self::MONDAY,
        2 => self::TUESDAY,
        3 => self::WEDNESDAY,
        4 => self::THURSDAY,
        5 => self::FRIDAY,
        6 => self::SATURNDAY,
    ];
}
