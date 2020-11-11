<?php
namespace Kd9703\Resources\Interfaces\Analyze;

use Kd9703\Entities\Analyze\DailyTotal as DailyTotalEntity;
use Kd9703\Entities\Analyze\DailyTotals;
use Kd9703\Entities\Media\Account;

/**
 * 日別統計
 */
interface DailyTotal
{
    // /**
    //  * 日別統計を取得する
    //  *
    //  * @param  Media  $media
    //  * @param  array  $tag_ids
    //  * @return Tags
    //  */
    // public function getList(Media $media, array $tag_ids): Tags;

    /**
     * シンプルに単一の日別統計情報を永続化する
     *
     * @param  Account $account
     * @return void
     */
    public function storeOne(DailyTotalEntity $daily_total): void;

    /**
     * 複数一括の日別統計情報を永続化する
     *
     * @param  Account $account
     * @return void
     */
    public function storeList(DailyTotals $daily_totals): void;
}
