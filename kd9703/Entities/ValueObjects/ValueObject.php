<?php

namespace Kd9703\Entities\ValueObjects;

use JsonSerializable;
use LogicException;

/**
 * DDDにおけるValueObject
 * セット時とゲット時にコールバックが呼べる
 * 値はセットされたら変更されない
 */
abstract class ValueObject implements JsonSerializable
{
    /**
     * @var mixed
     */
    protected $_value;

    /**
     * @param array $keyvalues
     */
    public function __construct($value)
    {
        $this->_value = $this->onSet($value);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->onGet();
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->onGet();
    }

    /**
     * @return mixed
     */
    public function toValue()
    {
        return $this->onGet();
    }

    /**
     * 値セット時にコールされる
     * セットしたい値を返す
     * @param $value
     */
    protected function onSet($value)
    {
        return $value;
    }

    /**
     * 値を取り出すときにコールされる
     * 取り出される値を返す
     * @return mixed
     */
    protected function onGet()
    {
        return $this->_value;
    }

    /**
     * セット時に値が不正なときにコールする共通メソッド
     * @param $value
     */
    protected function invalid($value)
    {
        throw new LogicException('invalide value ' . var_export($value, true));
    }
}
