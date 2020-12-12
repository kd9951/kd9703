<?php
namespace Kd9703\Usecases;

use Carbon\Carbon;
use Crawler\Support\Random;
use Crawler\Support\Timer;
use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account as AccountEntity;
use Kd9703\Logger\Interfaces\OwnerLogger;
use Kd9703\Logger\Interfaces\SystemLogger;
use Kd9703\MediaAccess\Interfaces\GetFollowers;
use Kd9703\Resources\Interfaces\Account\Account;
use Kd9703\Usecases\Usecase;

/**
 * なんとかして(w)新しいユーザーを探す
 */
final class GetNewUsers extends Usecase
{
    // ループ1回の処理件数 TwitterAPIの制限未満でできるだけたくさん
    const BULK_UNIT = 20;

    /**
     * 依存オブジェクトを受け取る
     */
    public function __construct(
        SetGlobalAccountRegulation $SetGlobalAccountRegulation,
        GetFollowers $GetFollowers,
        Account $Account,
        Random $random,
        Timer $timer,
        SystemLogger $systemLogger,
        OwnerLogger $ownerLogger
    ) {
        $this->usecases['SetGlobalAccountRegulation'] = $SetGlobalAccountRegulation;
        $this->mediaAccesses['GetFollowers']          = $GetFollowers;
        $this->resources['Account']                   = $Account;

        parent::__construct($random, $timer, $systemLogger, $ownerLogger);
    }

    /**
     * 実行
     */
    public function exec(AccountEntity $account, int $limit_sec): bool
    {
        $started_at = Carbon::now()->format('Y-m-d H:i:s');

        $this->timer->start('GetNewUsers', $limit_sec * 1000);

        // // 既存のフォロワ数の多いアカウント
        // $master_ids = [
        //     [1264452794758467584, 'salon_ukai2',     '鵜飼勇至',           ], // 14046
        //     [1259696867677368320, 'salon_esashika',  '江刺家',             ], // 10480
        //     [1259678482503614464, 'salon_matsuoka',  'まつろー',           ], // 14034
        //     [1261228709580693505, 'salon_meiro',     'おざわゆうしん',     ], // 14003
        //     [1259850565183516673, 'salon__yoshi',    'YOSHI',              ], // 13995
        //     [1260191427008446469, 'salonyahagi',     '矢萩',               ], // 13690
        //     [1307260129222389761, 'salon_1noue21',   'RYO_INOUE',          ], // 7569
        //     [1269253262038843392, 'salonsarubutsu',  '川村伸寛（猫和尚）', ], // 3444
        //     [1259673030441095171, 'salon_poupelle',  'べぇくん',           ], // 4823
        //     [1259669887514931200, 'salonyan1',       '柳澤ヤン',           ], // 6946
        //     [1259671491689369600, 'salonsetochan',   'セトちゃん',         ], // 4244
        // ];

        $master_ids = $this->mediaAccesses['GetFollowers']->getFollowerIds($account);

        shuffle($master_ids);
        reset($master_ids);

        while ($this->timer->remains('GetNewUsers')) {
            $count = 0;
            // $target_account = next($master_ids);
            // if (!$target_account) {
            //     break;
            // }
            // [$target_account_id, $target_account_username, $target_account_fullname] = $target_account;
            // $this->systemLogger->debug("Select $target_account_id ($target_account_username : $target_account_fullname) as a target.");

            $target_account_id = next($master_ids);
            if ($target_account_id === false) {
                $this->systemLogger->debug("No more followers.");
                break;
            }
            $this->systemLogger->debug("Select $target_account_id as a target.");

            $target_account = new AccountEntity(['account_id' => $target_account_id]);

            $existing_ids = $this->resources['Account']->getAllIds($account->media);

            $new_follows = $this->mediaAccesses['GetFollowers']([
                'account'             => $target_account,
                'limit'               => 60,
                'exclude_account_ids' => $existing_ids,
            ]);
            // 保存
            foreach ($new_follows as $new_follow) {
                $new_account = $new_follow->from_account;
                $new_account = $this->usecases['SetGlobalAccountRegulation'](['account' => $new_account]);

                // 既存のアカウントでなければ保存とカウント
                if (!in_array($new_account->account_id, $existing_ids)) {
                    $this->resources['Account']->upsert($new_account);
                    $count++;
                } else {
                    $this->systemLogger->warning("Existing Account $new_account->account_id has been regot.");
                    // せっかく取れたので保存はしておく
                    $this->resources['Account']->upsert($new_account);
                }
            }
            $this->systemLogger->info("$count 人のプロフィールを新規登録");
            $this->systemLogger->kpi('account-created', "$count");
        }

        return true;
    }

}
