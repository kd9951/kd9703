<?php

namespace Tests;

use ReflectionMethod;

trait InteractsWithProtectedMethod
{

    /**
     * protected や private をテストする
     * $object->$method( $args[0], $args[1], $args[2], ... ) というイメージ
     *
     * @param  object $object このオブジェクトのメソッドとして実行する
     * @param  string $method メソッド名
     * @param  array  $args   メソッドへの引数
     * @return mixed  $result メソッドの戻り値
     */
    protected function invokeMethod($object, string $method, array $args)
    {
        $method = new ReflectionMethod(get_class($object), $method);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $args);
    }

    /**
     * static な protected や private をテストする
     * $class::$method( $args[0], $args[1], $args[2], ... ) というイメージ
     *
     * @param  string $class  クラス名
     * @param  string $method メソッド名
     * @param  array  $args   メソッドへの引数
     * @return mixed  $result メソッドの戻り値
     */
    protected function invokeStaticMethod(string $class, string $method, array $args)
    {
        $method = new ReflectionMethod($class, $method);
        $method->setAccessible(true);
        return $method->invokeArgs(null, $args);
    }
}
