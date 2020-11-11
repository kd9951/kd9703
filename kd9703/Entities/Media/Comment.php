<?php
namespace Kd9703\Entities\Media;

use Kd9703\Constants\Media;
use Kd9703\Entities\Entity;

class Comment extends Entity
{
    /**
     * @var array
     */
    protected $attritubes = [
        'media'       => [Media::class, null],
        'post_id'     => ['string', null],
        'account_id'     => ['string', null],
        'title'       => ['string', null],
        'body'        => ['string', null],
        'count_liked' => ['integer', null],
        'reviewed_at' => ['date:Y-m-d H:i:s', null],
    ];
}
