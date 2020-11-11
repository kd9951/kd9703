<?php
namespace Kd9703\Entities\Media;

use Kd9703\Constants\Media;
use Kd9703\Constants\Notice\NoticeType;
use Kd9703\Entities\Entity;

class Notice extends Entity
{
    /**
     * @var array
     */
    protected $attritubes = [
        'media'           => [Media::class, null],
        'notice_id'       => ['integer', null],
        'account_id'      => ['?string', null],
        'account'         => ['?' . Account::class, null],
        'from_account_id' => ['?string', null],
        'from_account'    => ['?' . Account::class, null],
        'notified_at'     => ['date:Y-m-d H:i:s', null],
        'notice_type'     => [NoticeType::class, null],
        'title'           => ['string', null],
        'body'            => ['string', null],
        'post_id'         => ['?integer', null],
        'post'            => ['?' . Post::class, null],
    ];
}
