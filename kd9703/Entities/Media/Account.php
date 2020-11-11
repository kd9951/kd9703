<?php

namespace Kd9703\Entities\Media;

use Kd9703\Constants\LoginMethod;
use Kd9703\Constants\Media;
use Kd9703\Constants\Prefecture;
use Kd9703\Entities\Entity;
use Kd9703\Entities\ValueObjects\EncryptedPassword;

class Account extends Entity
{
    /**
     * @var array
     */
    protected $attritubes = [
        'media'                   => [Media::class, null],
        'account_id'              => ['string', null],
        'username'                => ['string', null],
        'fullname'                => ['string', null],
        'login_method'            => ['?' . LoginMethod::class, null],
        'login_id'                => ['?string', null],
        'password'                => ['?' . EncryptedPassword::class, null],
        'oauth_access_token'      => ['?string', null],
        'oauth_access_secret'     => ['?' . EncryptedPassword::class, null],
        'prefecture'              => ['?' . Prefecture::class, null],
        'web_url1'                => ['?string', null],
        'web_url2'                => ['?string', null],
        'web_url3'                => ['?string', null],
        'img_thumnail_url'        => ['?string', null],
        'img_cover_url'           => ['?string', null],
        'score'                   => ['?int', null],
        'total_post'              => ['?int', null],
        'total_follow'            => ['?int', null],
        'total_follower'          => ['?int', null],
        'total_likes'             => ['?int', null],
        'last_posted_at'          => ['?date:Y-m-d H:i:s', null],
        'reviewed_at'             => ['date:Y-m-d H:i:s', null],
        'is_private'              => ['bool', null],
        'is_salon_account'        => ['bool', null],
        'hidden_from_auto_follow' => ['bool', null],
        'hidden_from_search'      => ['bool', null],

        // その日時点での（1日の最後に記録して以後更新しない）
        'tracked_follow'   => ['?int', 0], // アプリが保持しているフォロー数
        'tracked_follower' => ['?int', 0], // アプリが保持しているフォロワー数
        'tracked_post'     => ['?int', 0], // アプリが保持している投稿数
    ];

    // セッタ（検証の実装）を書くならこのように
    // /**
    //  * @param $value
    //  */
    // public function setPassword($value): void
    // {
    //     throw new \LogicException();
    //     return $value;
    // }
}
