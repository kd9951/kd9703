<?php

namespace Tests;

use Mockery;

trait InteractsWithMockedService
{
    /**
     * クラスをモック化してサービスコンテナの結合を置き換える
     * モックがどう呼び出されるかのアサーションはコールバックで渡す
     *
     * [USAGE]
     *   $this->prepareMock( ServiceToBeMocked::class, function ($mock) { return $mock // クラス名以外はイディオム
     *     ->shouldReceive('classMethod') // メソッド
     *     ->once()  // 期待される呼び出し回数
     *     ->with(   // メソッドに渡される引数
     *         null,
     *         $this->asArray([
     *             "shop_id"           => 1,
     *             "program_id"        => 321,
     *             "episode_id"        => 3957,
     *         ]))
     *     ->andReturn(3235) // メソッドが返す返り値
     * ;});
     *
     * @param string $class  クラス名
     * @param string $method メソッド名
     */
    protected function prepareMock(string $class, callable $callback, string $abstract = '')
    {
        $abstract = $abstract ? $abstract : $class;
        $mock     = Mockery::mock($class);
        $mock     = $callback($mock);
        app()->instance($abstract, $mock);
    }

    /**
     * ↑のマクロ
     * クラスをモック化して、指定メソッドがコールされる【回数】だけチェック
     *
     * MEMO このメソッドは内部にアサーション系関数があるので assert...
     * ↑のはアサーションは外に定義するので prepare...
     *
     * [USAGE]
     *   $this->assertMockMethodCalled( ServiceToBeMocked::class, 'classMethod', 0 );
     *   $this->assertMockMethodCalled( ServiceToBeMocked::class, 'classMethod', 1, [1157] );
     *
     * @param string $class
     * @param string $method
     * @param int    $times           回数 省略されると1回以上。0だと呼ばれないことをチェック。
     * @param array  $andReturnValues 回数に応じた返り値の配列
     */
    protected function assertMockMethodCalled(string $class, string $method, /*<?int>*/ $times = null, array $andReturnValues = null)
    {
        $this->prepareMock($class, function ($mock) use ($method, $times, $andReturnValues) {
            $mock = $mock->shouldReceive($method);
            if ($times === null) {
                $mock = $mock->zeroOrMoreTimes();
            } else {
                $mock = $mock->times($times);
            }
            if (!empty($andReturnValues)) {
                $mock = $mock->andReturnValues($andReturnValues);
            }
            return $mock;
        });
    }

    /**
     * withで使うマクロ
     * 期待される引数を toArray して配列として照合する
     * モデルを保存しようとモックに渡しているときに。
     *
     * @param array $arg
     */
    protected function asArray(array $ex_array)
    {
        return Mockery::on(function ($arg) use ($ex_array) {
            $result = $arg->toArray() == $ex_array;
            if (!$result) {
                // 何が違うのかコンソールに出ないので…
                $this->assertEquals($ex_array, $arg->toArray(), 'The method\'s arguments matched no expected argument list for this method.');
            }
            return $result;
        });
    }

    /**
     * withで使うマクロ
     *
     * @param array $arg
     */
    protected function asAny()
    {
        return Mockery::any();
    }
}
