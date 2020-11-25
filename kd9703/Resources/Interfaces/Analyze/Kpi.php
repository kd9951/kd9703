<?php
namespace Kd9703\Resources\Interfaces\Analyze;

use Kd9703\Entities\Analyze\Kpi as KpiEntity;
use Kd9703\Entities\Analyze\Kpis;
use Kd9703\Entities\Media\Account;

/**
 * 日別統計
 */
interface Kpi
{
    public function generateNow(): KpiEntity;

    /**
     * 日別統計を取得する
     *
     * @param  Media  $media
     * @param  array  $tag_ids
     * @return Tags
     */
    public function getList(string $start_date, string $end_date = ''): Kpis;

    /**
     * シンプルに単一の日別統計情報を永続化する
     *
     * @param  Account $account
     * @return void
     */
    public function storeOne(KpiEntity $kpi): void;

    /**
     * 複数一括の日別統計情報を永続化する
     *
     * @param  Account $account
     * @return void
     */
    public function storeList(Kpis $kpis): void;
}
