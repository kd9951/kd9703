<?php

namespace Kd9703\Entities\Sort;

use Kd9703\Entities\Entity;

class Input extends Entity
{
    /**
     * @var array
     */
    protected $attritubes = [
        'key'   => 'string',
        'order' => 'string',
    ];

    const ASC  = 'asc';
    const DESC = 'desc';
}
