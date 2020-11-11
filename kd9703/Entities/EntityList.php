<?php

namespace Kd9703\Entities;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * 他のエンティティの配列
 */
abstract class EntityList extends Entity implements Iterator, ArrayAccess, Countable
{
    use EntityCast;

    /**
     * @var mixed
     */
    protected $_array_type = null;
    /**
     * @var array
     */
    protected $_array = [];

    /**
     * @param array $keyvalues
     */
    public function __construct(array $children)
    {
        assert(!is_a($this->_array_type, Entity::class, true), "array_type must be a Entity");
        assert(is_a($this->_array_type, EntityList::class, true), "array_type must not be a EntityList");

        $this->_array = [];
        // (ひとまず)内部配列のキーはJSの配列のように連番を維持するようにする
        $children = array_values($children);
        foreach ($children as $index => $value) {
            $this->__set($index, $value);
        }
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($index, $value)
    {
        $value = $this->_checkAndCast($index ?? 0, $this->_array_type, $value, $this->_array[$index] ?? []);
        if (is_null($index)) {
            $this->_array[] = $value;
        } else {
            // (ひとまず)内部配列のキーはJSの配列のように連番を維持するようにする
            if (!array_key_exists($index, $this->_array) && $index !== count($this->_array)) {
                throw new \LogicException("index $index was not defined nor over ranged.");
            }
            $this->_array[$index] = $value;
        }
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        $array = $this->_array;

        foreach ($array as $key => $value) {
            if ($value instanceof Entity || is_object($value) && method_exists($value, 'toArray')) {
                $array[$key] = $value->toArray();
            }
        }

        return $array;
    }

    //////////////////////////////////////
    // よく使う配列操作
    // array_xxxxx にあるが、EntityListでどうやるかをいちいち再検索するのは時間の無駄なので
    // EntityListならこうするというサンプルコードの代わりにできるだけ用意する

    /**
     * @param self $another
     */
    public function merge(self $another): self
    {
        if (!$another instanceof static ) {
            throw new \LogicException('object to be merged must be same class (' . get_class($this) . ') but ' . get_class($another) . ' given');
        }
        foreach ($another as $item) {
            $this->_array[] = $item;
        }

        return $this;
    }

    /**
     * 部分配列を取得する
     * @param self $another
     */
    public function slice(int $start, ?int $count = null): self
    {
    }

    /**
     * 一部を抜き取る
     * @param self $another
     */
    public function splice(int $start, ?int $count = null): self
    {
    }

    /**
     * @param self $another
     */
    public function pluck(string $key): array
    {
        $result = [];
        foreach ($this->_array as $item) {
            $result[] = $item->$key;
        }

        return $result;
    }

    /**
     * 最後の要素を１つ取り出す
     * @param self $another
     */
    public function pop()
    {
        $item = array_pop($this->_array);

        return $item;
    }

    /**
     * 最初の要素を１つ取り出す
     * @param self $another
     */
    public function shift()
    {
        $item = array_shift($this->_array);

        return $item;
    }

    /**
     * 順番をバラバラにする
     * @param self $another
     */
    public function suffle()
    {
        // 便利だけど、乱数生成がインフラに依存する
        throw new \LogicException();
    }

    // Iterator
    /**
     * @var int
     */
    protected $_current_key = 0;

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->_array[$this->_current_key];
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return $this->_current_key;
    }

    public function next(): void
    {
        $this->_current_key++;
    }

    public function rewind(): void
    {
        $this->_current_key = 0;
    }

    public function valid(): bool
    {
        return isset($this->_array[$this->_current_key]);
    }

    // ArrayAccess
    /**
     * @param $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->_array[$offset]);
    }

    /**
     * @param  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->_array[$offset] ?? null;
    }

    /**
     * @param $offset
     * @param $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->__set($offset, $value);
    }

    /**
     * @param $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->_array[$offset]);
        // 連番を維持する
        $this->_array = array_values($this->_array);
    }

    public function count(): int
    {
        return count($this->_array);
    }
}
