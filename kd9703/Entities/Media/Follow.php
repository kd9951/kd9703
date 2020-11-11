<?php

namespace Kd9703\Entities\Media;

use Kd9703\Constants\Follow\FollowedBackType;
use Kd9703\Constants\Follow\FollowMethod;
use Kd9703\Constants\Follow\UnFollowMethod;
use Kd9703\Constants\Media;
use Kd9703\Entities\Entity;

/**
 * 2つのアカウントを双方向に関連付けるフォロー＆フォローバックの記録
 */
class Follow extends Entity
{
    /**
     * @var array
     */
    protected $attritubes = [
        'media'              => [Media::class, null],
        'follow_id'          => ['integer', null],
        'from_account_id'    => ['string', null],
        'from_account'       => [Account::class, null],
        'to_account_id'      => ['string', null],
        'to_account'         => [Account::class, null],
        'followed_at'        => ['date:Y-m-d H:i:s', null],
        'follow_method'      => FollowMethod::class, // フォローした手段
        'followed_back'      => ['boolean', null],
        'followed_back_at'   => ['date:Y-m-d H:i:s', null],
        'followed_back_type' => FollowedBackType::class, // フォローされた理由
        'unfollowed'         => ['boolean', null],
        'unfollowed_at'      => ['date:Y-m-d H:i:s', null],
        'unfollowed_method'  => [UnFollowMethod::class, 0],
    ];
}
