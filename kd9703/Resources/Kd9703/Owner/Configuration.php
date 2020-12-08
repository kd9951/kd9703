<?php
namespace Kd9703\Resources\Kd9703\Owner;

use Kd9703\Constants\Media;
use Kd9703\Eloquents\Support\Configuration as ConfigurationModel;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Owner\Configration as ConfigrationsEntity;
use Kd9703\Resources\Interfaces\Owner\Configuration as ConfigurationInterface;
use Kd9703\Resources\Kd9703\Tools\EloquentAdapter;

/**
 * 設定
 */
class Configuration implements ConfigurationInterface
{
    use EloquentAdapter;

    const COLS = [
        'show_new_by',
        'show_new_days',
        'auto_follow',
        'target_follow_per_day',
        'sleep_hour_start',
        'sleep_hour_end',
        'auto_follow_back_before_accept',
        'auto_follow_in_my_list',
        'auto_follow_target_list',
        'auto_follow_members',
        'follow_only_official_regulation',
        'follow_only_set_icon',
        'follow_only_tweets_more_than',
        'follow_only_profile_contains',
        'follow_only_keyword_contains_1',
        'follow_only_keyword_contains_2',
        'follow_only_keyword_contains_3',
        'follow_again_in_days',
        'auto_follow_back',
        'auto_reject',
        'check_follower_regulation',
        'check_following_regulation',
        'follow_back_only_official_regulation',
        'follow_back_only_set_icon',
        'follow_back_only_tweets_more_than',
        'follow_back_only_profile_contains',
    ];

    // 便宜上、accountテーブルに保存している設定
    const COLS_ON_ACCOUNT = [
        'hidden_from_auto_follow',
        'hidden_from_search',
    ];

    /**
     * @param Account $account
     */
    public function create(Account $account): ConfigrationsEntity
    {
        $configration = new ConfigrationsEntity([]);

        $configration = $this->store($account, $configration);

        return $configration;
    }

    /**
     * @param Account $account
     */
    public function get(Account $account): ConfigrationsEntity
    {
        $eloquent = new ConfigurationModel();
        $model    = $eloquent->select(self::COLS)->where('account_id', $account->account_id)->first();

        if (!$model) {
            return new ConfigrationsEntity([]);
        }

        $configuration = new ConfigrationsEntity([]);

        foreach (self::COLS_ON_ACCOUNT as $col) {
            if (!is_null($model->$col)) {
                $configuration->$col = $model->$col;
            }
        }

        return $configuration;
    }

    /**
     * @param Account             $account
     * @param ConfigrationsEntity $configration
     */
    public function store(Account $account, ConfigrationsEntity $configration): ConfigrationsEntity
    {
        // アカウントに保存する設定
        $eloquent = $this->getEloquent(Media::TWITTER(), 'Account');
        $model    = $eloquent->find($account->account_id);

        foreach (self::COLS_ON_ACCOUNT as $col) {
            if (!is_null($configration->$col)) {
                $model->$col = $configration->$col;
            }
        }
        $model->save();

        // 設定
        $eloquent = new ConfigurationModel();
        $model    = $eloquent->find($account->account_id);

        if (!$model) {
            $model             = new $eloquent();
            $model->account_id = $account->account_id;
        }

        foreach (self::COLS as $col) {
            if (!is_null($configration->$col)) {
                $model->$col = $configration->$col;
            }
        }
        $model->save();

        return $configration;
    }
}
