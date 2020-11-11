<?php

namespace Kd9703\Constants;

class LoginMethod extends Enum
{
    const UNKNOWN     = 0;
    const ID_PW       = 1;
    const OAUTH       = 10;
    const OAUTH_TOKEN = 11;
    const FACEBOOK    = 21;
    const GOOGLE      = 22;
    const LINE        = 23;
    const TWITTER     = 24;

    const LIST = [
        self::UNKNOWN,
        self::ID_PW,
        self::OAUTH,
        self::OAUTH_TOKEN,
        self::FACEBOOK,
        self::GOOGLE,
        self::LINE,
        self::TWITTER,
    ];
}
