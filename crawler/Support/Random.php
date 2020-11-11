<?php
namespace Crawler\Support;

/**
 * 乱数発生器
 */
class Random
{
    /**
     * 正規分布に沿った乱数を生成する
     *
     * @param  float   $av 平均値
     * @param  float   $sd 標準偏差
     * @return float
     */
    public function uniform(int $min, int $max): float
    {
        $x = mt_rand() / mt_getrandmax();

        return ((int) ($max - $min + 1) * $x) + $min;
    }

    /**
     * 正規分布に沿った乱数を生成する
     * 本来は 0.27％ の確率で最小値と最大値を超えるが
     * 丸めてしまうので絶対に超えない
     * min と max 自体は含む
     *
     * @param  int $min
     * @param  int $max
     * @return int min ～ max の整数値
     */
    public function normalMinMax(int $min, int $max): int
    {
        $float = $this->normal(($max + $min) / 2, ($max + $min) / 2 / 3);
        $int   = (int) max($min, min($max, (int) $float));
        return $int;
    }

    /**
     * 正規分布に沿った乱数を生成する
     *
     * @param  float   $av 平均値
     * @param  float   $sd 標準偏差
     * @return float
     */
    public function normal(float $av, float $sd): float
    {
        $x = mt_rand() / mt_getrandmax();
        $y = mt_rand() / mt_getrandmax();
        return sqrt(-2 * log($x)) * cos(2 * pi() * $y) * $sd + $av;
    }
}
