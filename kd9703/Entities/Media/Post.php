<?php
namespace Kd9703\Entities\Media;

use Kd9703\Constants\Media;
use Kd9703\Entities\Entity;

class Post extends Entity
{
    /**
     * @var array
     */
    protected $attritubes = [
        'media'      => [Media::class, null],
        'is_private' => ['boolean', false],
        'post_id'    => ['string', null],

        'account_id' => ['string', null],
        'account'    => [Account::class, null],

        'in_reply_to_account_id' => ['string', null],
        'in_reply_to_account'    => [Account::class, null],

        'in_reply_to_post_id' => ['string', null],
        'in_reply_to_post'    => [Post::class, null],

        'recipient_account_ids' => ['array of string', null],

        'url'              => ['string', null],
        'title'            => ['string', null],
        'body'             => ['string', null],
        'img_thumnail_url' => ['string', null],
        'img_main_url'     => ['string', null],
        'score'            => ['integer', null],
        'count_liked'      => ['integer', null],
        'count_comment'    => ['integer', null],
        'count_shared'     => ['integer', null],
        'posted_at'        => ['date:Y-m-d H:i:s', null],
        'reviewed_at'      => ['date:Y-m-d H:i:s', null],
    ];
}
