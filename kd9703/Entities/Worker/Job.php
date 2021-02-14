<?php

namespace Kd9703\Entities\Worker;

use Kd9703\Constants\Job\Priority;
use Kd9703\Constants\Media;
use Kd9703\Entities\Entity;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Owner\Owner;

/** 
 * 永続化しない、Loggerに食わすだけのJOB
 */
class Job extends Entity
{
    /**
     * @var array
     */
    protected $attritubes = [
        'job_id'           => ['integer', null],
        // 'owner_id'         => ['string', null],
        // 'owner'            => [Owner::class, null],
        'media'            => [Media::class, null],
        'account_id'       => ['string', null],
        // 'account'          => [Account::class, null],
        // 'priority'         => [Priority::class, null],
        // 'rest_times'       => ['?int', null], // null は制限なし
        'job_class'        => ['string', null],
        // 'last_operated_at' => ['date:Y-m-d H:i:s', null],
        // 'closed'           => ['bool', null],
        // 'running'          => ['bool', null],
        // 'postponed_times'  => ['int', null],
        // 'next_cursor'      => ['?string', null],
    ];
}
