<?php
namespace Kd9703\Usecases;

use Carbon\Carbon;
use Crawler\Support\Random;
use Crawler\Support\Timer;
use Kd9703\Entities\Media\Account as AccountEntity;
use Kd9703\Logger\Interfaces\OwnerLogger;
use Kd9703\Logger\Interfaces\SystemLogger;
use Kd9703\Resources\Interfaces\Analyze\Kpi;
use Kd9703\Usecases\Usecase;

/**
 * なんとかして(w)新しいユーザーを探す
 */
final class UpdateKpi extends Usecase
{
    // ループ1回の処理件数 TwitterAPIの制限未満でできるだけたくさん
    const BULK_UNIT = 20;

    /**
     * 依存オブジェクトを受け取る
     */
    public function __construct(
        Kpi $Kpi,
        Random $random,
        Timer $timer,
        SystemLogger $systemLogger,
        OwnerLogger $ownerLogger
    ) {
        $this->resources['Kpi'] = $Kpi;

        parent::__construct($random, $timer, $systemLogger, $ownerLogger);
    }

    /**
     * 実行
     */
    public function exec(): bool
    {
        // 昨日（確定）
        $kpi = $this->resources['Kpi']->generate('-1 day');
        $this->resources['Kpi']->storeOne($kpi);

        // 今日（暫定）
        $kpi = $this->resources['Kpi']->generate('now');
        $this->resources['Kpi']->storeOne($kpi);

        $this->systemLogger->info("KPIを更新");

        return true;
    }

}
