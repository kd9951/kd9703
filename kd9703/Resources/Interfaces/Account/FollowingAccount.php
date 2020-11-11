<?php
namespace Kd9703\Resources\Interfaces\Account;

use Kd9703\Constants\Follow\FollowMethod;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Accounts;

// use Kd9703\Eloquents\Kd9703\Account;
// use Kd9703\Eloquents\Kd9703\Follow;

/**
 * フォローしているアカウント
 */
interface FollowingAccount
{
    /**
     * 現在フォローしているアカウント
     *
     * @param  Account    $account
     * @return Accounts
     */
    public function getList(Account $account): Accounts;

    /**
     * 現在フォローしてるアカウント数
     *
     * @param  Account $account
     * @return int
     */
    public function getTotal(Account $account): int;

    /**
     * 過去にフォローしていたことがあるアカウント
     *
     * @param  Account    $account
     * @return Accounts
     */
    public function getExFollowingList(Account $account): Accounts;

    /**
     * フォローしてはいけないブラックリストアカウントを取得
     *
     * @param  Account    $account
     * @return Accounts
     */
    public function getBlacklist(Account $account): Accounts;

    /**
     * フォロー解除すべきアカウント
     *
     * @param  Account    $account
     * @return Accounts
     */
    public function getToBeUnfollow(Account $account, int $limit = -1): Accounts;

    /**
     * フォローを実行
     *
     * @param  Account      $account
     * @param  Account      $target_account
     * @param  string       $start_datetime
     * @param  FollowMethod $followMethod
     * @return void
     */
    public function recordStartingFollow(Account $account, Account $target_account, string $start_datetime, FollowMethod $followMethod): void;
}
