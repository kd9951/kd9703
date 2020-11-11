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
        'media'            => [Media::class, null],
        'post_id'          => ['string', null],
        'account_id'       => ['string', null],
        'account'          => [Account::class, null],
        'url'              => ['string', null],
        'title'            => ['string', null],
        'body'             => ['string', null],
        'img_thumnail_url' => ['string', null],
        'img_main_url'     => ['string', null],
        'score'            => ['integer', null],
        'count_liked'      => ['integer', null],
        'count_comment'    => ['integer', null],
        'reviewed_at'      => ['date:Y-m-d H:i:s', null],
    ];
}
