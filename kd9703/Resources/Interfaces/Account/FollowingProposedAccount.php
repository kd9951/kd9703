<?php
namespace Kd9703\Resources\Interfaces\Account;

use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Accounts;

/**
 *
 */
interface FollowingProposedAccount
{
    const DEFAULT_LIMIT = -1;

    /**
     * ストックに追加する
     */
    public function addList(Account $account, Accounts $accounts): void;

    /**
     * 全ストック
     */
    public function getList(Account $account, int $limit = self::DEFAULT_LIMIT): Accounts;

    /**
     * ストックされている数
     */
    public function getTotal(Account $account): int;

    /**
     * 実行済にする
     * 次回のGetListから出てこなくなる
     * 削除しているかもしれない
     */
    public function markDone(Account $account, Accounts $accounts): void;
}
