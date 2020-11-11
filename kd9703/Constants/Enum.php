<?php
namespace Kd9703\Constants;

use JsonSerializable;
use LogicException;

abstract class Enum implements JsonSerializable
{
    const LIST = [
    ];

    /**
     * @var mixed
     */
    protected $_value;

    /**
     * @param array $keyvalues
     */
    public function __construct($value)
    {
        foreach (static::LIST as $candidate) {
            if ($candidate === $value) {
                $this->_value = $candidate;

                return;
            }
        }

        throw new LogicException('invalide value ' . var_export($value, true));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->_value;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->_value;
    }

    /**
     * @return mixed
     */
    public function toValue()
    {
        return $this->_value;
    }

    /**
     * Class::CONSTANT とすると通常の定数だが
     * Class::CONSTANT() とすると、その定数がセットされたインスタンスが返る
     *
     * Class::LIST() とするとすべてのインスタンスの配列が返る
     */
    public static function __callStatic($name, $arguments)
    {
        if ($name === 'LIST') {
            return array_map(function ($v) {return new static($v);}, static::LIST);
        }

        // $name = ltrim(strtoupper(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $name)), '_');
        return new static(constant("static::$name"));
    }
}
