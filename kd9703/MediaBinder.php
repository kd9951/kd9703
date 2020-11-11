<?php

namespace Kd9703;

use Kd9703\Constants\Media;

interface MediaBinder
{
    /**
     * この後に実行する Usecase, MediaAccess は
     * このメディア用のものをインジェクトせよ！
     * という魔法
     * 唱えるとフレームワークがそのように動く
     *
     * @param Media $media
     */
    public function bind(?Media $media);
}
