<?php
namespace Kd9703\Constants\Job;

use Kd9703\Constants\Enum;
use LogicException;

class Priority extends Enum
{
    // これはあくまで基準値で、使うときにずらすことが可能
    const HIGH    = 20;
    const REGULAR = 40;
    const LOW     = 60;

    const LIST = [
        self::HIGH,
        self::REGULAR,
        self::LOW,
    ];

    /**
     * @param array $keyvalues
     */
    public function __construct($value)
    {
        if (!is_int($value)) {
            throw new LogicException('invalide value ' . var_export($value, true));
        }

        $this->_value = $value;
    }

    /**
     * @param  $name
     * @param  $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $instance = parent::__callStatic($name, $arguments);

        // 引数を取ることが可能で、オフセットする
        if (isset($arguments[0])) {
            if ($arguments[0] < 0 || $arguments[0] >= 20) {
                throw new \LogicException('out of range.');
            }

            $instance->_value += $arguments[0];
        }
        return $instance;
    }

}
