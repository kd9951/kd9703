<?php

namespace Kd9703\Entities\ValueObjects;

use Illuminate\Support\Facades\Hash;

/**
 * Laravelのハッシュを使うので、Laravel以外の環境ではそれを定義すること
 * @use encrypt
 * @use decrypt
 */
class HashedPassword extends ValueObject
{
    /**
     * @param $value
     */
    public function onSet($value)
    {
        // ハッシュ化されているときはそのまま
        if ($this->hashedAlready($value)) {
            return $value;
        }

        return Hash::make((string) $value);
    }

    /**
     * 入力された文字列がハッシュ化されたものとマッチするか
     * ログインの直前でしか使用しない
     */
    public function check(string $plain_password): bool
    {
        return Hash::check($plain_password, $this->_value);
    }

    /**
     * @ref https://stackoverflow.com/questions/40034186/how-to-check-if-a-string-is-hashed-by-laravel-hash
     * @param string $str
     */
    protected function hashedAlready(string $str)
    {
        return preg_match('/^\$2y\$/', $str);
    }
}
