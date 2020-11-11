<?php

namespace Kd9703\Eloquents;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Str;

abstract class Model extends BaseModel
{
    use Concerns\ValidateOnSave;

    /**
     * @var mixed
     */
    protected $primaryKey = null;

    /**
     * @return mixed
     */
    public function getKeyName()
    {
        return $this->primaryKey ?? Str::snake(class_basename($this)) . '_id';
    }

    public function isNew()
    {
        // タイムスタンプを見ているので、タイムスタンプがないモデルには使用不可
        if ($this->timestamps == false) {
            throw new \LogicException('タイムスタンプがないモデルには使用不可');
        }

        // created に値があればレコード作成済
        return ($this->created_at) ? false : true;
    }

}
