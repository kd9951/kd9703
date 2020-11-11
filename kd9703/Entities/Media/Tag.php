<?php
namespace Kd9703\Entities\Media;

use Kd9703\Constants\Media;
use Kd9703\Entities\Entity;

class Tag extends Entity
{
    /**
     * @var array
     */
    protected $attritubes = [
        'media'          => [Media::class, null],
        'tag_id'         => ['string', null],
        'tag_body'       => ['string', null],
        'total_post'     => ['?int', null],
        'total_follower' => ['?int', null],
    ];
}
