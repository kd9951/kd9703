<?php

namespace Kd9703\Eloquents\Twitter;

use Kd9703\Eloquents\Model;

/**
 * シンプルにDBレコードの出し入れしかしないOwnerモデル
 * つまりLaravelのユーザー認証には関わらない
 **/
class Owner extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];
}
