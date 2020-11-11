<?php
namespace Kd9703\Resources\Kd9703\Account;

use DateTime;
use Kd9703\Constants\Follow\FollowMethod;
use Kd9703\Constants\Follow\UnFollowMethod;
use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Accounts;
use Kd9703\Resources\Kd9703\Tools\EloquentAdapter;
use Kd9703\Resources\Interfaces\Account\FollowingAccount as FollowingAccountInterface;
use Kd9703\Resources\Interfaces\Follow\Following;

/**
 * アカウント
 */
class FollowingAccount implements FollowingAccountInterface
{
    use EloquentAdapter;

    /**
     * @param Follow $follow
     */
    public function __construct(Following $following)
    {
        $this->Resources['Following'] = $following;
    }

    const ACCOUNT_COLUMNS = [
        'account_id',
        'username',
        'fullname',
        'prefecture',
        'web_url1',
        'web_url2',
        'web_url3',
        'img_thumnail_url',
        'img_cover_url',
        'score',
        'total_post',
        'total_follow',
        'total_follower',
        'total_likes',
        'last_posted_at',
        // 'reviewed_at',
        'is_private',
    ];

    /**
     * 現在フォローしてる
     * @param Account $account
     */
    public function getList(Account $account): Accounts
    {
        $eloquent = $this->getEloquent($account->media, 'Account');

        $follows        = $this->Resources['Following']->getList($account);
        $to_account_ids = array_column($follows->toArray(), 'to_account_id');
        $accounts       = $eloquent->select(self::ACCOUNT_COLUMNS)->whereIn('account_id', $to_account_ids)->get();

        return new Accounts($accounts->toArray());
    }

    /**
     * 現在フォローしてるアカウント数
     *
     * @param  Account $account
     * @return int
     */
    public function getTotal(Account $account): int
    {
        $eloquent = $this->getEloquent($account->media, 'Follow');
        $count    = $eloquent->currentFollow($account->account_id)->count();

        return $count;
    }

    /**
     * 過去にフォローしたことがある
     *
     * @param  Account    $account
     * @return Accounts
     */
    public function getExFollowingList(Account $account): Accounts
    {
        return new Accounts([]);
    }

    /**
     * フォローしてはいけないブラックリスト
     *
     * @param  Account    $account
     * @return Accounts
     */
    public function getBlacklist(Account $account): Accounts
    {
        return new Accounts([]);
    }

    /**
     * フォロー解除すべきアカウント
     *
     * @param  Account    $account
     * @return Accounts
     */
    public function getToBeUnfollow(Account $account, int $limit = -1): Accounts
    {
        if ($limit == 0) {
            return new Accounts([]);
        }

        $eloquent = $this->getEloquent($account->media, 'Account');

        $follows        = $this->Resources['Following']->getToBeUnfollow($account, $limit);
        $to_account_ids = array_column($follows->toArray(), 'to_account_id');
        $accounts       = $eloquent->select(self::ACCOUNT_COLUMNS)->whereIn('account_id', $to_account_ids)->get();

        return new Accounts($accounts->toArray());
    }

    /**
     * フォローを記録
     *
     * @param  Account      $account
     * @param  Account      $target_account
     * @param  string       $start_datetime
     * @param  FollowMethod $followMethod
     * @return Accounts
     */
    public function recordStartingFollow(Account $account, Account $target_account, string $start_datetime, FollowMethod $followMethod): void
    {
        $this->Resources['Following']->recordStartingFollow($account, $target_account, $start_datetime, $followMethod);
    }

    /**
     * フォロー解除を記録
     *
     * @param  Account      $account
     * @param  Account      $target_account
     * @param  string       $start_datetime
     * @param  FollowMethod $followMethod
     * @return Accounts
     */
    public function recordUnfollow(Account $account, Account $target_account, string $start_datetime, UnFollowMethod $unfollowMethod): void
    {
        $this->Resources['Following']->recordUnfollow($account, $target_account, $start_datetime, $unfollowMethod);
    }

}
