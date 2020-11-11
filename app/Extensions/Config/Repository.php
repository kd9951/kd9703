<?php
namespace App\Extensions\Config;

use Illuminate\Config\Repository as BaseRepository;

/**
 * Configが未定義だったら例外を投げるようにした拡張
 */
class Repository extends BaseRepository
{
    public function get($key, $default = null)
    {
        if (is_null($default) && !$this->has($key)) {
            throw new \LogicException("undefined config [$key].");
        }
        return parent::get($key, $default);
    }
}
