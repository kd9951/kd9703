<?php

namespace Kd9703\Constants;

class ShowNew extends Enum
{
    const HIDDEN        = 0;
    const BY_CREATED_AT = 1;
    const BY_STARTED_AT = 2;

    const LIST = [
        self::HIDDEN,
        self::BY_CREATED_AT,
        self::BY_STARTED_AT,
    ];

    const LABEL_JA = [
        self::HIDDEN        => '表示しない',
        self::BY_CREATED_AT => 'このアプリが発見して掲載した日',
        self::BY_STARTED_AT => 'Twitterのアカウント開設日',
    ];
}
