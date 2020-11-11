<?php
namespace Kd9703\Constants;

class Media extends Enum
{
    const DEFAULT    = 'default';
    const INSTAGRAM  = 'instagram';
    const GREEN_SNAP = 'green_snap';
    const TWITTER    = 'twitter';

    const LIST = [
        // DEFAULT はENUM値として認めていない（そんな媒体は無いので）ABSTRACTな値
        // 引数としてDEFAUT的なものを受け取りたいときは ?Media として NULL値 を受け取る
        self::INSTAGRAM,
        self::GREEN_SNAP,
        self::TWITTER,
    ];
}
