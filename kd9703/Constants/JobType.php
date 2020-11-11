<?php
namespace Kd9703\Constants;

use Kd9703\Usecases\Kd9703\Worker\Job\DailyJob;
use Kd9703\Usecases\Kd9703\Worker\Job\StoreAllFollowings;
use Kd9703\Usecases\Kd9703\Worker\Job\StoreAllFollwers;

class JobType extends Enum
{
    const DAILY                = 'daily';
    const STORE_ALL_FOLLOWINGS = 'StoreAllFollowings';
    const STORE_ALL_FOLLOWERS  = 'StoreAllFollwers';

    const LIST = [
        self::DAILY,
        self::STORE_ALL_FOLLOWINGS,
        self::STORE_ALL_FOLLOWERS,
    ];

    const CLASS_NAME = [
        self::DAILY                => DailyJob::class,
        self::STORE_ALL_FOLLOWINGS => StoreAllFollowings::class,
        self::STORE_ALL_FOLLOWERS  => StoreAllFollwers::class,
    ];

    public function getClassName()
    {
        return self::CLASS_NAME[$this->toValue()];
    }
}
