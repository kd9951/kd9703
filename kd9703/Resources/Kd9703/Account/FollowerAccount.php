<?php
namespace Kd9703\Resources\Kd9703\Account;

use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Accounts;
use Kd9703\Resources\Kd9703\Tools\EloquentAdapter;
use Kd9703\Resources\Interfaces\Account\FollowerAccount as FollowerAccountInterface;
use Kd9703\Resources\Interfaces\Follow\Follower;

/**
 * アカウント
 */
class FollowerAccount implements FollowerAccountInterface
{
    use EloquentAdapter;

    /**
     * @param Follow $follow
     */
    public function __construct(Follower $follower)
    {
        $this->Resources['Follower'] = $follower;
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

        $followers        = $this->Resources['Follower']->getList($account);
        $from_account_ids = array_column($followers->toArray(), 'from_account_id');
        $accounts         = $eloquent->select(self::ACCOUNT_COLUMNS)->whereIn('account_id', $from_account_ids)->get();

        return new Accounts($accounts->toArray());
    }

    /**
     * 現在フォローされているアカウント数
     *
     * @param  Account $account
     * @return int
     */
    public function getTotal(Account $account): int
    {
        $eloquent = $this->getEloquent($account->media, 'Follow');
        $count    = $eloquent->currentFollower($account->account_id)->count();

        return $count;
    }

    /**
     * フォローされたことを記録
     *
     * @param Account      $account
     * @param Account      $target_account
     * @param string       $start_datetime
     * @param FollowMethod $followMethod
     */
    public function recordFollowed(Account $account, Account $follower_account, string $start_datetime): void
    {
        $this->Resources['Follower']->recordFollowed($account, $follower_account, $start_datetime);
    }

}
