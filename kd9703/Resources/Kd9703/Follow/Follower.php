<?php
namespace Kd9703\Resources\Kd9703\Follow;

use DateTime;
use Kd9703\Constants\Media;
use Kd9703\Entities\Follow;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Follows;
use Kd9703\Resources\Kd9703\Tools\EloquentAdapter;
use Kd9703\Resources\Interfaces\Follow\Follower as FollowerInterface;

/**
 * フォローされている
 */
class Follower implements FollowerInterface
{
    use EloquentAdapter;

    const FOLLOW_COLUMNS = [
        // 'media',
        'follow_id',
        'from_account_id',
        // 'from_account',
        'to_account_id',
        // 'to_account',
        'followed_at',
        // 'follow_method', // 所有権がないため
        'followed_back',
        'followed_back_at',
        'followed_back_type',
        'unfollowed',
        'unfollowed_at',
        'unfollowed_method',
    ];

    /**
     * 現在フォローされている
     * @param Account $account
     */
    public function getList(Account $account): Follows
    {
        $eloquent = $this->getEloquent($account->media, 'Follow');

        $eloquent = $eloquent->where('unfollowed', 0)
            ->where('to_account_id', $account->account_id);

        $follows = $eloquent->select(self::FOLLOW_COLUMNS)->get()->toArray();

        foreach ($follows as $idx => $follow) {
            $follows[$idx]['media'] = $account->media;
        }

        return new Follows($follows);
    }

    /**
     * フォローされたことを記録
     * 「フォロワを追加 addFollwer」と同義
     *
     * @param Account $account
     * @param Account $follower_account
     * @param string  $start_datetime
     */
    public function recordFollowed(Account $account, Account $follower_account, string $start_datetime): void
    {
        $eloquent = $this->getEloquent($account->media, 'Follow');

        $eloquent->followBack(
            $follower_account->account_id,
            $account->account_id,
            new DateTime($start_datetime)
        );
    }

}
