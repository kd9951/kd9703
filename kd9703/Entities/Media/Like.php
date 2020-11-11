<?php
namespace Kd9703\Entities\Media;

use Kd9703\Constants\Media;
use Kd9703\Entities\Entity;

class Like extends Entity
{
    /**
     * @var array
     */
    protected $attritubes = [
        'media'           => [Media::class, null],
        'like_id'         => ['integer', null],
        'from_account_id' => ['integer', null],
        'from_account'    => [Account::class, null],
        'to_account_id'   => ['integer', null],
        'to_account'      => [Account::class, null],
        'to_post_id'      => ['integer', null],
        'to_post'         => [Post::class, null],
        'liked_at'        => ['date:Y-m-d H:i:s', null],
        'like_method'     => ['integer', null],
        'unliked'         => ['integer', null],
        'unliked_at'      => ['integer', null],
        'unliked_method'  => ['integer', null],
    ];
}
