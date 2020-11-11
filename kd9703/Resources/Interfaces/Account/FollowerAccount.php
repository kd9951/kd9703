<?php
namespace Kd9703\Resources\Interfaces\Account;

use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Accounts;

/**
 * フォローされているアカウント
 * フォロワーさん
 */
interface FollowerAccount
{
    /**
     * 現在フォローされているアカウント
     *
     * @param  Account    $account
     * @return Accounts
     */
    public function getList(Account $account): Accounts;

    /**
     * 現在フォローされているアカウント数
     *
     * @param  Account    $account
     * @return Accounts
     */
    public function getTotal(Account $account): int;

    /**
     * フォローされたことを記録
     * 「フォロワを追加 addFollwer」と同義
     *
     * @param Account $account
     * @param Account $follower_account
     * @param string  $start_datetime
     */
    public function recordFollowed(Account $account, Account $follower_account, string $start_datetime): void;
}
