<?php
namespace Kd9703\Resources\Kd9703\Analyze;

use Carbon\Carbon;
use Kd9703\Constants\Media;
use Kd9703\Eloquents\Support\SystemLog;
use Kd9703\Entities\Analyze\Kpi as KpiEntity;
use Kd9703\Entities\Analyze\Kpis;
use Kd9703\Entities\Media\Account;
use Kd9703\Resources\Interfaces\Analyze\Kpi as KpiInterface;
use Kd9703\Resources\Kd9703\Tools\EloquentAdapter;

/**
 * 日別統計
 */
class Kpi implements KpiInterface
{
    use EloquentAdapter;

    const KPI_COLUMNS = [
        'date',
        'accounts_total',
        'salon_accounts_total',
        'salon_accounts_active',
        'registered_accounts_total',
        'registered_accounts_active',
        'rejected_accounts_total',
        'reviewed_accounts',
        'created_accounts',
        'started_accounts_2w',
        'api_called_total',
        'oldest_review_datetime',
    ];

    /**
     * @return mixed
     */
    public function generate(string $php_time_string): KpiEntity
    {
        $account    = $this->getEloquent(Media::TWITTER(), 'Account');
        $system_log = new SystemLog();

        $kpi = new KpiEntity([]);

        $day = Carbon::parse($php_time_string);

        $start_1d = $day->format('Y-m-d 00:00:00');
        $end_1d   = $day->format('Y-m-d 23:59:59');
        $start_2w = (new Carbon($day))->subDay(13)->format('Y-m-d 00:00:00');
        $end_2w   = $day->format('Y-m-d 23:59:59');

        $kpi->date                       = $day->format('Y-m-d');
        $kpi->accounts_total             = $account->where('created_at', '<=', $end_1d)->count();
        $kpi->salon_accounts_total       = $account->where('created_at', '<=', $end_1d)->where('is_salon_account', 1)->count();
        $kpi->salon_accounts_active      = null;
        $kpi->registered_accounts_total  = $account->whereNotNull('oauth_access_token')->count();
        $kpi->registered_accounts_active = null;
        $kpi->rejected_accounts_total    = $account->where('hidden_from_auto_follow', 1)->orWhere('hidden_from_search', 1)->count();
        $kpi->started_accounts_2w        = $account->whereBetween('started_at', [$start_2w, $end_2w])->count();
        $kpi->reviewed_accounts          = $system_log->whereBetween('created_at', [$start_1d, $end_1d])->where('level', 'kpi-account-reviewed')->sum('message');
        $kpi->created_accounts           = $account->whereBetween('created_at', [$start_1d, $end_1d])->where('is_salon_account', 1)->count();
        $kpi->api_called_total           = $system_log->whereBetween('created_at', [$start_1d, $end_1d])->where('level', 'media_access')->where('message', 'like', '%CALL%')->count();

        $reviewed_at                 = $account->orderBy('reviewed_at', 'asc')->first()->reviewed_at ?? null;
        $reviewed_at                 = $reviewed_at instanceof \DateTime ? $reviewed_at->format('Y-m-d H:i:s') : null;
        $kpi->oldest_review_datetime = $reviewed_at;

        return $kpi;
    }

    /**
     * 日別統計を取得する
     *
     * @param  Media  $media
     * @param  array  $daily_total_ids
     * @return Tags
     */
    public function getList(string $start_date, string $end_date = '', string $order = 'desc'): Kpis
    {
        $eloquent = $this->getEloquent(null, 'Kpi');

        $eloquent = $eloquent
            ->where('date', '>=', date('Y-m-d', strtotime($start_date)));

        if ($end_date) {
            $eloquent = $eloquent->where('date', '<=', date('Y-m-d', strtotime($end_date)));
        }

        $kpis = $eloquent->select(self::KPI_COLUMNS)->orderBy('date', $order)->get()->toArray();

        return new Kpis($kpis);
    }

    /**
     * シンプルに単一の日別統計情報を永続化する
     *
     * @param  Account $account
     * @return void
     */
    public function storeOne(KpiEntity $kpi): void
    {
        $eloquent = $this->getEloquent(null, 'Kpi');

        $model = $eloquent
            ->where('date', $kpi->date)
            ->first();

        if (!$model) {
            $model       = new $eloquent();
            $model->date = $kpi->date;
        }

        foreach (self::KPI_COLUMNS as $key) {
            if (isset($kpi->$key) && !is_null($kpi->$key)) {
                $model->$key = $kpi->$key;
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
    public function storeList(Kpis $kpis): void
    {
        // FIXME ループしてたら一括する意味ないよね
        foreach ($kpis as $daily_total) {
            $this->storeOne($daily_total);
        }
    }
}
