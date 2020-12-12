<?php
namespace Kd9703\Usecases;

use Carbon\Carbon;
use Crawler\Support\Random;
use Crawler\Support\Timer;
use Kd9703\Entities\Media\Account as AccountEntity;
use Kd9703\Logger\Interfaces\OwnerLogger;
use Kd9703\Logger\Interfaces\SystemLogger;
use Kd9703\MediaAccess\Interfaces\GetPosts;
use Kd9703\MediaAccess\Interfaces\GetUsers;
use Kd9703\Resources\Interfaces\Account\Account;
use Kd9703\Resources\Interfaces\Post\Post;
use Kd9703\Resources\Interfaces\Transaction;
use Kd9703\Usecases\Usecase;

/**
 * 利用中アカウントの非公開情報を含んだ詳細情報を取得・更新
 */
final class UpdateUsingUser extends Usecase
{
    /**
     * 依存オブジェクトを受け取る
     */
    public function __construct(
        SetGlobalAccountRegulation $SetGlobalAccountRegulation,
        GetPosts $GetPosts,
        GetUsers $GetUsers,
        Account $Account,
        Post $Post,
        Transaction $Transaction,
        Random $random,
        Timer $timer,
        SystemLogger $systemLogger,
        OwnerLogger $ownerLogger
    ) {
        $this->usecases['SetGlobalAccountRegulation'] = $SetGlobalAccountRegulation;
        $this->mediaAccesses['GetPosts']              = $GetPosts;
        $this->mediaAccesses['GetUsers']              = $GetUsers;
        $this->resources['Post']                      = $Post;
        $this->resources['Account']                   = $Account;
        $this->resources['Transaction']               = $Transaction;

        parent::__construct($random, $timer, $systemLogger, $ownerLogger);
    }

    /**
     * 実行
     */
    public function exec(AccountEntity $account, int $limit_sec): bool
    {
        $started_at = Carbon::now()->format('Y-m-d H:i:s');

        $this->timer->start('UpdateUsingUser', $limit_sec * 1000);

        try {
            $this->resources['Transaction']->beginTransaction();

            $latest_post = $this->resources['Post']->getLatest($account);

            // 過去3日分は、RTやfav数がよく動くので取得・更新する
            // (3日でAPI1回分の200ポストを超えることはほぼないけど)
            $since_datetime = $latest_post ? min(Carbon::parse('-3 days')->format('Y-m-d H:i:s'), $latest_post->posted_at) : null;

            $this->systemLogger->debug("Latest post posted at " . ($since_datetime ?? 'NULL'));

            $new_posts = $this->mediaAccesses['GetPosts']([
                'account'        => $account,
                'limit'          => 10000,
                'since_datetime' => $since_datetime,
            ]);

            // 存在しないアカウントを取得
            $account_ids = [];
            foreach ($new_posts as $post) {
                $account_ids[$post->account_id] = $post->account_id;
                if ($post->in_reply_to_account_id) {
                    $account_ids[$post->in_reply_to_account_id] = $post->in_reply_to_account_id;
                }
                if (!empty($post->recipient_account_ids)) {
                    foreach ($post->recipient_account_ids as $account_id) {
                        $account_ids[$account_id] = $account_id;
                    }
                }
            }

            $new_accounts = $this->resources['Account']->getNotExists($account->media, array_values($account_ids));
            if ($new_accounts->count() > 0) {
                $new_accounts = $this->mediaAccesses['GetUsers']([
                    'account'         => $account,
                    'target_accounts' => $new_accounts,
                ]);
                // 更新があったものだけ保存
                $count = 0;
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

                $this->systemLogger->info("存在しなかったアカウント $count 人のプロフィールを取得");
            }

            // 投稿を保存
            $this->resources['Post']->storeMany($new_posts);

            $this->systemLogger->info($new_posts->count() . " 件の投稿を取得・更新");

            $this->resources['Transaction']->commit();

        } catch (\Exception $e) {
            $this->resources['Transaction']->rollback();

            $this->systemLogger->error($e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'code' => $e->getCode(),
                'body' => (string) $e,
            ]);

            echo $e;
        }

        // タイムスタンプを更新
        $account->reviewed_as_using_user_at = Carbon::now()->format('Y-m-d H:i:s');
        $this->resources['Account']->upsert($account);

        return true;
    }

}
