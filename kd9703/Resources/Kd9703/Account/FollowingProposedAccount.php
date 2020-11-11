<?php
namespace Kd9703\Resources\Kd9703\Account;

use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Accounts;
use Kd9703\Resources\Interfaces\Account\FollowingProposedAccount as FollowingProposedAccountInterface;
use Illuminate\Contracts\Cache\Repository as CacheContract;

/**
 *
 */
class FollowingProposedAccount implements FollowingProposedAccountInterface
{
    /**
     * @param Cache $cache
     */
    public function __construct(CacheContract $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param Account $account
     */
    private function cacheKey(Account $account): string
    {
        return "following_proposed_account.{$account->account_id}";
    }

    /**
     *
     */
    public function addList(Account $account, Accounts $accounts): void
    {
        $key = $this->cacheKey($account);

        $stored = $this->getList($account, 0);

        $stored->merge($accounts);

        $this->cache->put($key, $stored);
    }

    /**
     *
     */
    public function getList(Account $account, int $limit = self::DEFAULT_LIMIT): Accounts
    {
        $key = $this->cacheKey($account);

        $stored = $this->cache->get($key, null);
        $stored = $stored ?: new Accounts([]);

        if ($limit > 0) {
            $stored = new Accounts(array_slice($stored->toArray(), 0, $limit));
        }

        return $stored;
    }

    /**
     *
     */
    public function getTotal(Account $account): int
    {
        // 本体を全取得するならTOTALを事前チェックする必要がないだって？
        // TOTAL数を別に保存する実装をする可能性だってあるだろう
        $stored = $this->getList($account, 0);

        return count($stored);
    }

    /**
     * 実行済にする
     * 次回のGetListから出てこなくなる
     * 削除しているかもしれない
     */
    public function markDone(Account $account, Accounts $accounts): void
    {
        $stored = $this->getList($account, 0);

        $target_ids = array_column($accounts->toArray(), 'account_id');

        // EntityListが歯抜けのインデックスを詰めてくれるので
        $new = /*array_values*/(array_filter($stored->toArray(), function ($v) use ($target_ids) {
            return !in_array($v['account_id'], $target_ids);
        }));

        $key = $this->cacheKey($account);
        $this->cache->put($key, new Accounts($new));
    }
}
