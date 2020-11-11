<?php
namespace Kd9703\Logger\Interfaces;

use Kd9703\Entities\Worker\Job;
use Psr\Log\LoggerInterface;

/**
 * オーナー用ログ 各ユーザーに通知される
 */
interface OwnerLogger extends LoggerInterface
{
    // ▽ 全オーナー
    // EMERGENCY
    // ALERT
    // CRITICAL システム側の都合で機能不全 復旧を待て

    // ▽ ログイン中オーナーのみ
    // ERROR    処理中断 設定ミス 何かしら設定変更を促す
    // WARNING  一部失敗 設定ミスの可能性 変更すれば改善するかも
    // NOTICE   機能不十分 フォロータグ不足などで十分な数をスクレピングできなかった
    // INFO
    // DEBUG

    /**
     * ジョブ開始を記録
     */
    public function startJob(Job $job, int $serial);

    /**
     * ジョブ終了を記録
     */
    public function endJob(Job $job);
}
