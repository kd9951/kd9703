<?php
namespace Tests;

/**
 * Trait ArrayMergeEx
 */
trait ArrayMergeEx
{
    protected $_UNSET = '___UNSET___';

    /**
     * デフォルトのJSONを上書きするためのマージメソッド
     * array_merge_recursive と違って unset できる
     *
     * @param array $default
     * @param array $override
     *
     * @return array
     */
    private function arrayMergeEx(array $default, array $override) : array
    {
        $isSeqArray = function ($arr) {
            if (! is_array($arr)) {
                return false;
            }
            if ([] === $arr) {
                return false;
            }

            return array_keys($arr) === range(0, count($arr) - 1);
        };

        // 置き換え後がシーケンス配列であるか、置き換え元がシーケンスで空配列と置き換えようとするときはまるごと置き換える
        if ($isSeqArray($override) || ($isSeqArray($default) && empty($override))) {
            if (in_array($this->_UNSET, $override)) {
                throw new \Exception('インデックス配列のなかにUNSETを含めることはできません（削除対象が不明瞭）');
            }

            return $override;
        }

        foreach ($override as $idx => $value) {
            if (isset($default[$idx]) && is_array($default[$idx]) && is_array($value)) {
                $default[$idx] = $this->arrayMergeEx($default[$idx], $value);
                continue;
            }
            if ($value === $this->_UNSET) {
                unset($default[$idx]);
                continue;
            }
            $default[$idx] = $value;
        }

        return $default;
    }
}
