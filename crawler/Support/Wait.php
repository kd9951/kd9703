<?php
namespace Crawler\Support;

use Crawler\Support\Random;

/**
 * 前回から規定時間経過していなければ
 * その分を待つタイマー
 */
class Wait
{
    /**
     * @var array
     */
    protected static $microtimes = [];
    /**
     * @var array
     */
    protected static $total = [];

    /**
     * 正規分布に従うミリ秒数待つ
     * 本来は 0.27％ の確率で最小値と最大値を超えるが
     * 丸めてしまうので絶対に超えない
     *
     * @param  string $key
     * @param  int    $min
     * @param  int    $max
     * @return int    待ったミリ秒数
     */
    public function waitNormal(string $key, int $min, int $max): int
    {
        $mili_seconds = (new Random())->normalMinMax($min, $max);
        return $this->wait($key, $mili_seconds);
    }

    /**
     * 常に一定のミリ秒数待機する
     *
     * @param string $key
     * @param int    $microtime
     */
    public function wait(string $key, int $mili_seconds): int
    {
        $prev = self::$microtimes[$key] ?? 0;
        $period = (microtime(true) - $prev) * 1000;

        $wait = (int) max(0, $mili_seconds - $period);

        usleep($wait * 1000);

        self::$microtimes[$key] = microtime(true);
        self::$total[$key] = (self::$total[$key] ?? 0) + $wait / 1000;

        return $wait;
    }

    /**
     * @param  string  $key
     * @return mixed
     */
    public function getTotal(string $key = '')
    {
        return $key ? self::$total[$key] ?? 0 : self::$total;
    }
}
