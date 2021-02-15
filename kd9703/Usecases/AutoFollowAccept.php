<?php
namespace Kd9703\Usecases;

use Carbon\Carbon;
use Crawler\Support\Random;
use Crawler\Support\Timer;
use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account as AccountEntity;
use Kd9703\Logger\Interfaces\OwnerLogger;
use Kd9703\Logger\Interfaces\SystemLogger;
use Kd9703\MediaAccess\Interfaces\AcceptFollowerIncoming;
use Kd9703\MediaAccess\Interfaces\DenyFollowerIncoming;
use Kd9703\MediaAccess\Interfaces\GetFollowersIncoming;
use Kd9703\MediaAccess\Interfaces\GetUsers;
use Kd9703\Resources\Interfaces\Account\Account;
use Kd9703\Usecases\Usecase;

/**
 * 自分へのフォローリクエストを自動承認する
 */
final class AutoFollowAccept extends Usecase
{
    // 1回の処理件数 フォローする数にもよるが1日20件程度あれば十分
    const BULK_UNIT = 50;

    /**
     * 依存オブジェクトを受け取る
     */
    public function __construct(
        SetGlobalAccountRegulation $SetGlobalAccountRegulation,
        Account $Account,
        GetFollowersIncoming $GetFollowersIncoming,
        GetUsers $GetUsers,
        AcceptFollowerIncoming $AcceptFollowerIncoming,
        DenyFollowerIncoming $DenyFollowerIncoming,
        Random $random,
        Timer $timer,
        SystemLogger $systemLogger,
        OwnerLogger $ownerLogger
    ) {
        $this->usecases['SetGlobalAccountRegulation']  = $SetGlobalAccountRegulation;
        $this->resources['Account']                    = $Account;
        $this->mediaAccesses['GetFollowersIncoming']   = $GetFollowersIncoming;
        $this->mediaAccesses['GetUsers']               = $GetUsers;
        $this->mediaAccesses['AcceptFollowerIncoming'] = $AcceptFollowerIncoming;
        $this->mediaAccesses['DenyFollowerIncoming']   = $DenyFollowerIncoming;

        parent::__construct($random, $timer, $systemLogger, $ownerLogger);
    }

    /**
     * 実行
     */
    public function exec(
        AccountEntity $account,
        int $limit_sec,
        bool $auto_follow_back = true,
        bool $auto_reject = false,
        int $follow_back_only_tweets_more_than = -1,
        string $follow_back_only_profile_contains = ''
    ): bool {
        $started_at = Carbon::now()->format('Y-m-d H:i:s');

        $this->timer->start('AutoFollowAccept', $limit_sec * 1000);

        if (!$auto_follow_back && !$auto_reject) {
            $this->systemLogger->debug("自動承認・自動拒否ともにOFFのため処理をスキップ");
            return true;
        }

        // 承認待ちのユーザーを取得
        $target_accounts = $this->mediaAccesses['GetFollowersIncoming']([
            'account' => $account,
        ]);
        // dd($target_accounts->pluck('account_id'));

        if (empty($target_accounts)) {
            $this->systemLogger->debug("承認待ちのユーザーがいないのでスキップ");
            return true;
        }

        // そのユーザーの詳細情報を取得
        $target_accounts = $this->mediaAccesses['GetUsers']([
            'account'         => $account,
            'target_accounts' => $target_accounts,
        ]);
        // dd($target_accounts->toArray());

        // 既存のアカウント（新規登録件数のカウントのため）
        $existing_ids = $this->resources['Account']->getAllIds($account->media);

        // 更新があったものを保存
        $count_new    = 0;
        $count_update = 0;
        foreach ($target_accounts as $new_account) {
            $new_account = $this->usecases['SetGlobalAccountRegulation'](['account' => $new_account]);

            if (!in_array($new_account->account_id, $existing_ids)) {
                $this->resources['Account']->upsert($new_account);
                $this->systemLogger->debug("{$new_account->account_id} 新しいアカウントとして登録");
                $count_new++;
            } elseif ($new_account->reviewed_at >= $started_at) {
                $this->resources['Account']->upsert($new_account);
                // $this->systemLogger->debug("{$new_account->account_id} 既存アカウント");
                $count_update++;
            } else {
                // タイムスタンプのみ更新しておく（おそらく削除されたアカウント）
                $new_account->reviewed_at = $started_at;
                $this->resources['Account']->upsert($new_account);
                $this->systemLogger->debug("{$new_account->account_id} 削除されたアカウントっぽいが更新");
            }
        }
        $this->systemLogger->info("$count_new 人のプロフィールを新規登録");
        $this->systemLogger->kpi('account-created', "$count_new");
        $this->systemLogger->info("$count_update 人のプロフィールを最新化");
        $this->systemLogger->kpi('account-reviewed', "$count_update");

        // ループして
        $count_accept = 0;
        $count_deny   = 0;
        foreach ($target_accounts as $target_account) {
            // 条件にマッチするかチェック
            if ($this->matched($target_account, $follow_back_only_tweets_more_than, $follow_back_only_profile_contains)) {
                // 承認
                if ($auto_follow_back) {
                    $target_accounts = $this->mediaAccesses['AcceptFollowerIncoming']([
                        'account'           => $account,
                        'target_account_id' => $target_account->account_id,
                    ]);
                    $count_accept++;
                    $this->ownerLogger->info("{$target_account->fullname}（@{$target_account->username}）さんのフォローリクエストを承認しました。");
                }

            } else {
                // 拒否
                if ($auto_reject) {
                    $target_accounts = $this->mediaAccesses['DenyFollowerIncoming']([
                        'account'           => $account,
                        'target_account_id' => $target_account->account_id,
                    ]);
                    $count_deny++;
                    $this->ownerLogger->notice("{$target_account->fullname}（@{$target_account->username}）さんのフォローリクエストは条件にマッチしなかったため拒否しました。");
                } else {
                    $this->ownerLogger->warning("{$target_account->fullname}（@{$target_account->username}）さんのフォローリクエストは承認条件にマッチしていません。");
                }
            }
        }

        if ($count_accept > 0) {
            $this->ownerLogger->notice("$count_accept 人のフォローリクエストを承認しました。");
        }
        $this->systemLogger->info("$count_accept 人を自動承認");
        $this->systemLogger->kpi('account-accept', "$count_accept");

        if ($count_deny > 0) {
            $this->ownerLogger->notice("$count_deny 人のフォローリクエストを拒否しました。");
        }
        $this->systemLogger->info("$count_deny 人を自動拒否");
        $this->systemLogger->kpi('account-accept', "$count_deny");

        return true;
    }

    /**
     * 承認・拒否する条件
     *
     * @param  AccountEntity $account
     * @param  integer       $follow_back_only_tweets_more_than
     * @param  string        $follow_back_only_profile_contains
     * @return boolean
     */
    private function matched(
        AccountEntity $target_account,
        int $follow_back_only_tweets_more_than,
        string $follow_back_only_profile_contains
    ): bool {
        return $target_account->is_salon_account
        && ($target_account->total_post > $follow_back_only_tweets_more_than)
        && $this->matchKeywords(
            "{$target_account->username} {$target_account->fullname} {$target_account->description}",
            $follow_back_only_profile_contains
        );
    }

    /**
     * @param $body
     * @param $keywords
     */
    private function matchKeywords($body, $keywords): bool
    {
        $keywords = preg_replace('/[#＃]/u', '#', $keywords);
        $body     = preg_replace('/[#＃]/u', '#', $body);

        $keywords = mb_convert_kana($keywords, 'saKV');
        $keywords = preg_replace('/[、，]/u', ',', $keywords);
        $keywords = preg_replace('/[\s;:,]+/', ' ', $keywords);

        if ($keywords) {
            // すべてのキーワードを含む
            foreach (explode(' ', $keywords) as $keyword) {
                // ということは、ひとつでも含まれていなかったらFALSE
                if (strpos($body, $keyword) === false) {
                    return false;
                }
            }
        }

        return true;
    }

}
