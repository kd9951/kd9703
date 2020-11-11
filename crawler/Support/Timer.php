<?php
namespace Crawler\Support;

/**
 * スタートしてから、指定秒数経過したかを判定するラーメンタイマー
 */
class Timer
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
     * 許容時間をミリ秒でセットしてスタートする
     *
     * @param string $key
     * @param int    $microtime
     */
    public function start(string $key, int $mili_seconds): void
    {
        self::$microtimes[$key] = microtime(true) + $mili_seconds / 1000;
    }

    /**
     * 許容時間がまだ残っているか？
     *
     * @param  string  $key
     * @return mixed
     */
    public function remains(string $key): bool
    {
        return $this->rest($key) > 0;
    }

    /**
     * 残り時間（ミリ秒）
     *
     * @param  string  $key
     * @return integer $milisec 負値は残り時間を経過したミリ秒数
     */
    public function rest(string $key): int
    {
        return (self::$microtimes[$key] - microtime(true)) * 1000;
    }

    /**
     * 残り時間（秒）
     *
     * @param  string  $key
     * @return integer $milisec 負値は残り時間を経過したミリ秒数
     */
    public function rest_sec(string $key): int
    {
        return floor(self::$microtimes[$key] - microtime(true));
    }
}
