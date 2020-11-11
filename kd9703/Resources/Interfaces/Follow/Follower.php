<?php
namespace Kd9703\Resources\Interfaces\Follow;

use Kd9703\Constants\Follow\FollowedBackType;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Follows;

// use Kd9703\Eloquents\Kd9703\Account;
// use Kd9703\Eloquents\Kd9703\Follow;

/**
 * フォローされている
 */
interface Follower
{
    /**
     * @param  Account   $account
     * @return Follows
     */
    public function getList(Account $account): Follows;

    /**
     * フォローされたことを記録
     * 「フォロワを追加 addFollwer」と同義
     *
     * @param  Account          $account
     * @param  Account          $target_account
     * @param  string           $start_datetime
     * @param  FollowedBackType $followedBackType
     * @return void
     */
    public function recordFollowed(Account $account, Account $target_account, string $start_datetime): void;

}
