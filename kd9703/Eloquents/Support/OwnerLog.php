<?php

namespace Kd9703\Eloquents\Support;

use Kd9703\Eloquents\Model;

class OwnerLog extends Model
{
    /**
     * @var mixed
     */
    public $timestamps = false;

    /**
     * @var string
     */
    public $primaryKey = 'id';

    /**
     * 独自コネクション
     * @var string
     */
    protected $connection = 'mysql_for_log';

    protected static function boot()
    {
        parent::boot();

        config(['database.connections.mysql_for_log' =>
            config('database.connections.' . config('database.default')),
        ]);
    }
}
