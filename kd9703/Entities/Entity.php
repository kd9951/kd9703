<?php

namespace Kd9703\Entities;

/*
# 指定できる型と入力できる型
'notice_id'       => ['integer', null],       int型   ：整数値かnumericな文字列
'average'         => ['double', null],        double型：実数か、numericな文字列
'media'           => [Media::class, null],    Enum型  ：インスタンスかEnum内の値

'noticed_at'      => ['date', null],                   date文字列型：strtotimeできる文字列か、DateTimeのインスタンス。Y-m-d H:i:s 型になる
'noticed_at'      => ['date:Y-m-d H:i:s', null],       date文字列型：同じ形式の文字列か、DateTimeのインスタンス。文字列では / と - を区別するので厳しい

# nullable の考え方
'account_id'      => ['string', null],     NULL入力を許可しない文字列：nullは「セットされていない」のみ表すのでNULLだったら保存などの操作では無視する
'account_id'      => ['?string', null],    NULL入力を許可：後からの「NULLセット」を許可するので、NULLだったらDBクリアする。それ以外のケースでは使用しない。
'body'            => ['string', ''],       NULLを許可しない文字列

# 初期値の考え方
'score'       => ['integer', 0],       「データがなければゼロとする」という明確なロジックがあればセットする（例えばその種のショッピング件数） 更新のたびにセットしないとゼロにされる
'score'       => ['integer', null],    そうでなければ「未定義」としてnullにするのが基本（たとえば新規ユーザーのポスト数：存在しないことが証明された0のではなくチェックしていないnull）

# 初期値省略の考え方
'follow_method'      => FollowMethod::class,       エンティティによって存在しないことがあるパラメータ（フォロワとして得たFollowのフォロー理由）
 */

abstract class Entity
{
    use EntityCast;

    /**
     * @var array
     */
    protected $attritubes = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param array $keyvalues
     */
    public function __construct(array $keyvalues)
    {
        foreach ($keyvalues as $key => $value) {
            $this->__set($key, $value);
        }

        foreach ($this->attritubes as $key => $definication) {
            if (is_array($definication) && array_key_exists(1, $definication) && !array_key_exists($key, $this->data)) {
                $this->data[$key] = $definication[1];
            }
        }
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set(string $name, $value)
    {
        $definication = $this->attritubes[$name] ?? null;
        if (!$definication) {
            throw new \LogicException("attribute $name is not defined on " . get_called_class());
        }
        $definication = is_array($definication) ? $definication[0] : $definication;

        $this->data[$name] = $this->_checkAndCast($name, $definication, $value, $this->data[$name] ?? null);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __get(string $name)
    {
        if (!array_key_exists($name, $this->data)) {
            throw new \LogicException("$name is undefined.");
        }

        return $this->data[$name];
    }

    /**
     * @param $name
     * @param $value
     */
    public function __isset(string $name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __unset(string $name)
    {
        unset($this->data[$name]);
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        $array = $this->data;

        foreach ($array as $key => $value) {
            if ($value instanceof self || is_object($value) && method_exists($value, 'toArray')) {
                $array[$key] = $value->toArray();
            }
            if (is_object($value) && method_exists($value, 'toValue')) {
                $array[$key] = $value->toValue();
            }
        }

        return $array;
    }

    /**
     * @return mixed
     */
    public function getKeys()
    {
        return array_keys($this->attritubes);
    }
}
