<?php

namespace Kd9703\Entities\Worker;

use Kd9703\Entities\Entity;

class JobResult extends Entity
{
    /**
     * @var array
     */
    protected $attritubes = [
        'is_completed'    => ['bool', null],
        'next_cursor'     => ['string', null],
        'estimated_total' => ['integer', null],
        'proceeded_total' => ['integer', null],
    ];

}
