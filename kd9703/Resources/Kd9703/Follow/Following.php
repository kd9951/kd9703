<?php
namespace Kd9703\Resources\Kd9703\Follow;

use Carbon\Carbon;
use DateTime;
use Kd9703\Constants\Follow\FollowMethod;
use Kd9703\Constants\Follow\UnFollowMethod;
use Kd9703\Constants\Media;
use Kd9703\Eloquents\GreenSnap\Notice;
use Kd9703\Entities\Follow;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Accounts;
use Kd9703\Entities\Media\Follows;
use Kd9703\Resources\Kd9703\Tools\EloquentAdapter;
use Kd9703\Resources\Interfaces\Follow\Following as FollowingInterface;

/**
 * フォローしている
 */
class Following implements FollowingInterface
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
        'follow_method',
        'followed_back',
        'followed_back_at',
        // 'followed_back_type', // 所有権がないため
        'unfollowed',
        'unfollowed_at',
        'unfollowed_method',
    ];

    /**
     * 現在フォローしてる
     * @param Account $account
     */
    public function getList(Account $account): Follows
    {
        $eloquent = $this->getEloquent($account->media, 'Follow');
        $eloquent = $eloquent->where('unfollowed', 0)
            ->where('from_account_id', $account->account_id);

        $follows = $eloquent->select(self::FOLLOW_COLUMNS)->get()->toArray();

        foreach ($follows as $idx => $follow) {
            $follows[$idx]['media'] = $account->media;
        }

        return new Follows($follows);
    }

    /**
     * 過去にフォローしたことがある
     *
     * @param  Account   $account
     * @return Follows
     */
    public function getExFollowingList(Account $account): Follows
    {
        $eloquent = $this->getEloquent($account->media, 'Follow');
        $eloquent = $eloquent->where('unfollowed', 1)
            ->where('from_account_id', $account->account_id);
        // 最近フォローした人を再フォローすることを避けるためフォローしたのが24週以上前の人に限る
        // ->where('created_at', '>=', date('Y-m-d', strtotime('-24 weeks')));
        // とするのはAutoFollowのときだけでは？

        $follows = $eloquent->select(self::FOLLOW_COLUMNS)->get()->toArray();

        foreach ($follows as $idx => $follow) {
            $follows[$idx]['media'] = $account->media;
        }

        return new Follows($follows);
    }

    /**
     * 解除すべきフォロー
     *
     * @param  Account    $account
     * @return Accounts
     */
    public function getToBeUnfollow(Account $account, int $limit = -1): Follows
    {
        if ($limit == 0) {
            return new Follows([]);
        }

        $eloquent = $this->getEloquent($account->media, 'Follow');

        // 過去40日 1回以上エンゲージメントしている人
        $repeatusers = Notice::select('from_account_id', \DB::raw('count(*) as count'))
            ->whereIn('notice_type', [1, 3, 4])
            ->where('created_at', '>', new DateTime('-40 days'))
            ->groupBy('from_account_id')
            ->having('count', '>', 0)
            ->pluck('from_account_id');

        // Unfollowすべきユーザーは…
        // すべてのフォロー
        $eloquent = $eloquent::ownedBy($account->account_id)
        // 自分があとからフォローした人
        // ->where('followed_at', '>', \DB::raw('`followed_back_at`'))
        // ->get();
        // フォローされていない
        // ->where('followed_back', 0)
        // いいねもコメントもしていない
        // ->whereNotIn('to_account_id', Notice::whereIn('notice_type', [1, 4])->pluck('from_account_id'))
        // コメントしていない
        // ->whereNotIn('to_account_id', Notice::whereIn('notice_type', [4])->pluck('from_account_id'))
        // ○日以上経過している。
            ->where('followed_at', '<', Carbon::parse('-46 hours'))
        // 上記 リピートユーザー
            ->whereNotIn('to_account_id', $repeatusers)
        //     ->count();
        // dd($follows);
        ;

        if ($limit > 0) {
            $eloquent = $eloquent->limit($limit);
        }
        $eloquent = $eloquent->orderBy('followed_at', 'asc');
        $follows  = $eloquent->select(self::FOLLOW_COLUMNS)->get()->toArray();

        foreach ($follows as $idx => $follow) {
            $follows[$idx]['media'] = $account->media;
        }

        return new Follows($follows);
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
        $eloquent = $this->getEloquent($account->media, 'Follow');

        $eloquent->follow(
            $account->account_id,
            $target_account->account_id,
            new DateTime($start_datetime),
            (string) $followMethod
        );
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
        $eloquent = $this->getEloquent($account->media, 'Follow');

        $eloquent->unfollow(
            $account->account_id,
            $target_account->account_id,
            new DateTime($start_datetime),
            (string) $unfollowMethod
        );
    }
}
