<?php

namespace Kd9703\Entities\ValueObjects;

use Illuminate\Contracts\Encryption\DecryptException;

/**
 * Laravelの暗号化復号化ヘルパを使うので、Laravel以外の環境ではそれを定義すること
 * @use encrypt
 * @use decrypt
 */
class EncryptedPassword extends ValueObject
{
    /**
     * @param $value
     */
    public function onSet($value)
    {
        // 復号化できる＝すでに暗号化されているなら、そのままセットする
        // Laravel の decrypt ができないときだけ例外を投げるので try-catch している
        try {
            decrypt($value);
        } catch (DecryptException $e) {
            return encrypt((string) $value);
        }
        return $value;
    }

    /**
     * アカウントのパスワードを復号化して使用する
     * ログインの直前でしか使用しない
     */
    public function getPlainPassword()
    {
        return decrypt($this->_value);
    }
}
