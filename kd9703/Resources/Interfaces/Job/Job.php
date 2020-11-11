<?php
namespace Kd9703\Resources\Interfaces\Job;

use Kd9703\Constants\Job\Priority;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Owner\Owner;
use Kd9703\Entities\Worker\Job as JobEntity;

/**
 * ジョブ
 */
interface Job
{
    /**
     * Undocumented function
     *
     * @param  Owner       $owner
     * @return JobEntity
     */
    public function getNextJob(Owner $owner): ?JobEntity;

    /**
     * Undocumented function
     *
     * @param  JobEntity $job
     * @return void
     */
    public function store(Owner $owner, Account $account, string $job_class, Priority $priority, ?int $times = null): void;

    /**
     * Undocumented function
     *
     * @param  JobEntity $job
     * @return void
     */
    public function update(JobEntity $job): void;
}
