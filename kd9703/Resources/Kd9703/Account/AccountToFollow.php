<?php
namespace Kd9703\Resources\Kd9703\Account;

use Kd9703\Eloquents\Kd9703\Account as AccountEloquent;
use Kd9703\Entities\Account;
use Kd9703\Entities\Accounts;

/**
 *
 */
class AccountToFollow
{
    /**
     * 依存オブジェクトを受け取る
     */
    public function __construct(
        AccountEloquent $eloquent_account
    ) {
        $this->eloquent_account = $eloquent_account;
    }

    /**
     * 実行
     */
    public function addList(Account $user, Accounts $accounts): void
    {

    }

    /**
     * 実行
     */
    public function getList(Account $user, Accounts $accounts, int $limit): void
    {

    }

    /**
     * 実行済にする
     * 次回のGetListから出てこなくなる
     * 削除しているかもしれない
     */
    public function markDone(Account $user, Accounts $accounts): void
    {

    }
}
