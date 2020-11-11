<?php
namespace Kd9703\Resources\Kd9703\Analyze;

use Kd9703\Constants\Media;
use Kd9703\Entities\Analyze\DailyTotal as DailyTotalEntity;
use Kd9703\Entities\Analyze\DailyTotals;
use Kd9703\Entities\Media\Account;
use Kd9703\Resources\Kd9703\Tools\EloquentAdapter;
use Kd9703\Resources\Interfaces\Analyze\DailyTotal as DailyTotalInterface;

/**
 * 日別統計
 */
class DailyTotal implements DailyTotalInterface
{
    use EloquentAdapter;

    const DAILY_TOTAL_COLUMNS = [
        'account_id',
        'date',
        'total_follow',
        'total_follower',
        'total_post',
        'tracked_follow',
        'tracked_follower',
        'tracked_post',
        'total_like',
        'average_like',
        'average_like_by_post',
        'followed',
        'followed_auto',
        'followed_manual',
        'followed_back',
        'followed_back_organic',
        'followed_back_auto',
        'followed_back_manual',
        'unfollowed',
        'unfollowed_auto',
        'unfollowed_manual',
        'followed_and_back',
        'followed_and_back_auto',
        'followed_and_back_manual',
    ];
    /**
     * 日別統計を取得する
     *
     * @param  Media  $media
     * @param  array  $daily_total_ids
     * @return Tags
     */
    public function getList(Account $account, string $start_date, string $end_date = ''): DailyTotals
    {
        $eloquent = $this->getEloquent($account->media, 'DailyTotal');

        $eloquent = $eloquent->where('account_id', $account->account_id)
            ->where('date', '>=', date('Y-m-d', strtotime($start_date)));

        if ($end_date) {
            $eloquent = $eloquent->where('date', '<=', date('Y-m-d', strtotime($end_date)));
        }

        $daily_totals = $eloquent->select(self::DAILY_TOTAL_COLUMNS)->orderBy('date', 'desc')->get()->toArray();

        foreach ($daily_totals as $idx => $d) {
            $daily_totals[$idx]['media'] = Media::GREEN_SNAP();

            $daily_totals[$idx]['follow_back_ratio']        = $d['followed'] ? ($d['followed_and_back'] / $d['followed']) : 0.0;
            $daily_totals[$idx]['follow_back_auto_ratio']   = $d['followed_auto'] ? ($d['followed_and_back_auto'] / $d['followed_auto']) : 0.0;
            $daily_totals[$idx]['follow_back_manual_ratio'] = $d['followed_manual'] ? ($d['followed_and_back_manual'] / $d['followed_manual']) : 0.0;
        }

        return new DailyTotals($daily_totals);
    }

    /**
     * シンプルに単一の日別統計情報を永続化する
     *
     * @param  Account $account
     * @return void
     */
    public function storeOne(DailyTotalEntity $daily_total): void
    {
        // TODO バリデーションルール
        // if (!isset($daily_total)) {
        //     return;
        // }

        $eloquent = $this->getEloquent($daily_total->media, 'DailyTotal');

        $model = $eloquent->where('account_id', $daily_total->account_id)
            ->where('date', $daily_total->date)
            ->first();

        $model = $model ?: $eloquent;

        $daily_total = $daily_total->toArray();
        $daily_total = array_filter($daily_total, function ($v) {return !is_null($v);});
        unset($daily_total['media']);

        foreach (self::DAILY_TOTAL_COLUMNS as $key) {
            if (isset($daily_total[$key])) {
                $model->$key = $daily_total[$key];
            }
        }

        $model->save();

    }

    /**
     * 複数一括の日別統計情報を永続化する
     *
     * @param  Account $account
     * @return void
     */
    public function storeList(DailyTotals $daily_totals): void
    {
        // FIXME ループしてたら一括する意味ないよね
        foreach ($daily_totals as $daily_total) {
            $this->storeOne($daily_total);
        }
    }
}
