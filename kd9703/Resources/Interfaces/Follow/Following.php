<?php
namespace Kd9703\Resources\Interfaces\Follow;

use Kd9703\Constants\Follow\FollowMethod;
use Kd9703\Constants\Follow\UnFollowMethod;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Follows;

// use Kd9703\Eloquents\Kd9703\Account;
// use Kd9703\Eloquents\Kd9703\Follow;

/**
 * フォローしているアカウント
 */
interface Following
{
    /**
     * @param  Account   $account
     * @return Follows
     */
    public function getList(Account $account): Follows;

    /**
     * @param  Account   $account
     * @return Follows
     */
    public function getExFollowingList(Account $account): Follows;

    /**
     * @param  Account      $account
     * @param  Account      $target_account
     * @param  string       $start_datetime
     * @param  FollowMethod $followMethod
     * @return void
     */
    public function recordStartingFollow(Account $account, Account $target_account, string $start_datetime, FollowMethod $followMethod): void;

    /**
     * 解除すべきフォロー
     *
     * @param  Account    $account
     * @return Accounts
     */
    public function getToBeUnfollow(Account $account, int $limit = -1): Follows;

    /**
     * @param  Account        $account
     * @param  Account        $target_account
     * @param  string         $start_datetime
     * @param  UnFollowMethod $unfollow_method
     * @return void
     */
    public function recordUnfollow(Account $account, Account $target_account, string $start_datetime, UnFollowMethod $unfollowMethod): void;
}
