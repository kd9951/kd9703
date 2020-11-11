<?php

namespace Kd9703\Eloquents\Support;

use Kd9703\Eloquents\Model;

class Configuration extends Model
{
    /**
     * @var mixed
     */
    public $timestamps = false;

    /**
     * @var string
     */
    public $primaryKey = 'account_id';
}
