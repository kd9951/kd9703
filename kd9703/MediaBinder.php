<?php

namespace Kd9703;

use Kd9703\Entities\Media\Account;

interface MediaBinder
{
    /**
     * この後に実行する Usecase, MediaAccess は
     * このアカウント用のものをインジェクトせよ！
     * という魔法
     * 唱えるとフレームワークがそのように動く
     *
     * @param Media $media
     */
    public function bind(?Account $account);
}
