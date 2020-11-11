<?php
namespace Kd9703\Resources\Interfaces\Like;

use Kd9703\Entities\Media\Account;

/**
 * いいね
 */
interface Like
{
    /**
     * その日のいいね数を取得
     *
     * @param  Account   $account
     * @param  string    $date      集計対象日 なければ開始からの総計
     * @return integer
     */
    public function getCount(Account $account, string $date = null): int;
}
