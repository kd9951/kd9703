<?php
namespace Kd9703\Resources\Kd9703\Job;

use Kd9703\Constants\Job\Priority;
use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account as AccountEntity;
use Kd9703\Entities\Owner\Owner;
use Kd9703\Entities\Worker\Job as JobEntity;
use Kd9703\Resources\Kd9703\Tools\EloquentAdapter;
use Kd9703\Resources\Interfaces\Account\Account as AccountResource;
use Kd9703\Resources\Interfaces\Job\Job as JobInterface;

/**
 * ジョブ
 */
class Job implements JobInterface
{
    use EloquentAdapter;

    /**
     * @param Follow $follow
     */
    public function __construct(AccountResource $AccountResource)
    {
        $this->Resources['Account'] = $AccountResource;
    }

    const JOB_COLUMNS = [
        'job_id',
        'owner_id',
        'media',
        'account_id',
        'priority',
        'rest_times',
        'job_class',
        'last_operated_at',
        'closed',
        'running',
        'postponed_times',
        'next_cursor',
    ];

    /**
     * Undocumented function
     *
     * @param  Owner       $owner
     * @return JobEntity
     */
    public function getNextJob(Owner $owner): ?JobEntity
    {
        $eloquent = $this->getEloquent(null, 'Job');

        $job = $eloquent->where('owner_id', $owner->owner_id)
            ->where('closed', 0)
            ->where('running', 0)
            ->orderBy('priority', 'asc')
            ->select(self::JOB_COLUMNS)->first();

        if (!$job) {
            return null;
        }

        $job = new JobEntity($job->toArray());

        $job->owner   = $owner;
        $job->account = $this->Resources['Account']->getOne($job->media, $job->account_id);

        return $job;
    }

    /**
     * Undocumented function
     *
     * @param  JobEntity $job
     * @return void
     */
    public function store(Owner $owner, AccountEntity $account, string $job_class, Priority $priority, ?int $times = null): void
    {
        // TODO バリデーションルール
        if (!isset($owner->owner_id)) {
            return;
        }

        $eloquent = $this->getEloquent(null, 'Job');

        $model = new $eloquent();

        $model->owner_id         = $owner->owner_id;
        $model->media            = $account->media;
        $model->account_id       = $account->account_id;
        $model->priority         = $priority->toValue();
        $model->rest_times       = $times;
        $model->job_class        = $job_class;
        $model->last_operated_at = null;
        $model->closed           = false;
        $model->running          = false;
        $model->postponed_times  = 0;
        $model->next_cursor      = null;

        $model->save();
    }

    /**
     * Jobを更新
     * storeでセットされる初期値は変更できない
     *
     * @param  JobEntity $job
     * @return void
     */
    public function update(JobEntity $job): void
    {
        $eloquent = $this->getEloquent(null, 'Job');

        $model = new $eloquent();

        $model = $eloquent->find($job->job_id);
        $model = $model ?: $eloquent;

        foreach (array_filter([
            'last_operated_at' => $job->last_operated_at,
            'closed'           => $job->closed,
            'running'          => $job->running,
            'postponed_times'  => $job->postponed_times,
            'next_cursor'      => $job->next_cursor,
        ], function ($v) {return !is_null($v);}) as $key => $value) {
            $model->$key = $value;
        }

        $model->save();
    }
}
