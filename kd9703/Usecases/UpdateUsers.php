<?php
namespace Kd9703\Usecases;

use Carbon\Carbon;
use Crawler\Support\Random;
use Crawler\Support\Timer;
use Kd9703\Constants\Media;
use Kd9703\Logger\Interfaces\OwnerLogger;
use Kd9703\Logger\Interfaces\SystemLogger;
use Kd9703\MediaAccess\Interfaces\GetUsers;
use Kd9703\MediaBinder;
use Kd9703\Resources\Interfaces\Account\Account;
use Kd9703\Usecases\Usecase;

/**
 * DBのユーザー情報を古いものから順に、最新情報にアップデートしていく
 */
final class UpdateUsers extends Usecase
{
    // ループ1回の処理件数 TwitterAPIの制限未満でできるだけたくさん
    const BULK_UNIT = 20;

    /**
     * 依存オブジェクトを受け取る
     */
    public function __construct(
        SetGlobalAccountRegulation $SetGlobalAccountRegulation,
        GetUsers $GetUsers,
        Account $Account,
        MediaBinder $MediaBinder,
        Random $random,
        Timer $timer,
        SystemLogger $systemLogger,
        OwnerLogger $ownerLogger
    ) {
        $this->usecases['SetGlobalAccountRegulation'] = $SetGlobalAccountRegulation;
        $this->mediaAccesses['GetUsers']              = $GetUsers;
        $this->resources['Account']                   = $Account;
        $this->MediaBinder                            = $MediaBinder;

        parent::__construct($random, $timer, $systemLogger, $ownerLogger);
    }

    /**
     * 実行
     */
    public function exec(int $limit_sec): bool
    {
        $started_at = Carbon::now()->format('Y-m-d H:i:s');

        $this->timer->start('UpdateUsers', $limit_sec * 1000);

        $account = $this->resources['Account']->getSystemAccount(Media::TWITTER());

        $this->MediaBinder->bind($account);

        $count = 0;
        while ($this->timer->remains('UpdateUsers')) {
            $target_accounts = $this->resources['Account']->getOlds(Media::TWITTER(), self::BULK_UNIT);

            $new_accounts = $this->mediaAccesses['GetUsers']([
                'account'         => $account,
                'target_accounts' => $target_accounts,
            ]);
            // 更新があったものだけ保存
            foreach ($new_accounts as $new_account) {
                $new_account = $this->usecases['SetGlobalAccountRegulation'](['account' => $new_account]);

                if ($new_account->reviewed_at >= $started_at) {
                    $this->resources['Account']->upsert($new_account);
                    $count++;
                } else {
                    // タイムスタンプのみ更新しておく（おそらく削除されたアカウント）
                    $new_account->reviewed_at = $started_at;
                    $this->resources['Account']->upsert($new_account);
                }
            }
        }

        $this->systemLogger->info("$count 人のプロフィールを最新化");

        return true;
    }

}
