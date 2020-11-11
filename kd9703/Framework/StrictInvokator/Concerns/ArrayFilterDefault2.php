<?php
namespace Kd9703\Framework\StrictInvokator\Concerns;

use App\Usecases\Exceptions\SyntaxErrorException;

/**
 * 与えられた配列を、指定されたフォーマットの配列に合わせる
 * アスタリスクキー対応と、
 * 空配列まわりの細かな挙動を調整するためローテクPHPコードで書き直したもの
 */
trait ArrayFilterDefault2
{
    /**
     * 与えられた配列を、指定されたフォーマットの配列に合わせる
     */
    protected function ArrayFilterDefault(array $defaults, array $options)
    {
        $defaults = $this->extractDefault($defaults);

        $filtered_options = $this->filterArrayRecursive($defaults, $options);
        $results          = $this->mergeArrayRecursive($defaults, $filtered_options);

        return $results;
    }

    private function mergeArrayRecursive($defaults, $input, $result = [])
    {
        if (empty($defaults)) {
            return [];
        }

        // * を入力配列に合わせて展開
        if (array_key_exists('*', $defaults)) {
            foreach($input as $key => $child) {
                $defaults[$key] = $defaults['*'];
            }
            unset($defaults['*']);
        }

        // フィルタがシーケンスではなく、子がないか配列だったら再起実行
        foreach(array_keys(array_merge($defaults,$input)) as $key) {
            if (isset($defaults[$key]) 
                && is_array($defaults[$key]) && !$this->isSequenceArray($defaults[$key]) 
                 && (!isset($input[$key]) || is_array($input[$key])) //&& !$this->isSequenceArray($child)
            ) {
                $input[$key] = $this->mergeArrayRecursive($defaults[$key], $input[$key] ?? []);
            }
        }

        foreach($defaults as $key => $child) {
            if (! array_key_exists($key, $input) && !is_null($child)) {
                $input[$key] = $child;
            }
        }

        return $input;
    }

    /**
     * フィルターにないキーを除去する
     */ 
    private function filterArrayRecursive(array $filter, array $array)
    {
        if (empty($filter)) {
            return [];
        }

        // * を入力配列に合わせて展開
        if (array_key_exists('*', $filter)) {
            foreach($array as $key => $child) {
                $filter[$key] = $filter['*'];
            }
            unset($filter['*']);
        }

        // フィルタと子がシーケンスではない配列だったら再起実行
        foreach($array as $key => $child) {
            if (isset($filter[$key]) && is_array($filter[$key]) 
                && !$this->isSequenceArray($filter[$key]) && is_array($child) 
                && !$this->isSequenceArray($child)
            ) {
                $array[$key] = $this->filterArrayRecursive($filter[$key], $child);
            }
        }

        $result = array_filter($array, function($key) use ($filter) {
            return array_key_exists($key, $filter);
        }, ARRAY_FILTER_USE_KEY);

        return $result;
    }

    private $_extracted_default = null;

    private function extractDefault($default)
    {
        return !is_null($this->_extracted_default) ? $this->_extracted_default : $this->_extracted_default = $this->extractDot($default, []); 
    }

    private function extractDot(array $array, array $result = [])
    {
        foreach($array as $key => $value){
            $newkeys = explode('.', $key, 2);
            if (!empty($newkeys[1])) {
                $key = $newkeys[0];
                $value = [$newkeys[1] => $value];
            }
            if (is_array($value) && !$this->isSequenceArray($value)) {
                $value = $this->extractDot($value, $result[$key] ?? []);
            }
            if ($key === '*' && !array_key_exists('*', $result) && !empty($result)) {
                throw new SyntaxErrorException('* と他の一般キーがあるところには使用できません ');
            }
            if ($key !== '*' && array_key_exists('*', $result)) {
                throw new SyntaxErrorException('一般キーは * があるところには使用できません');
            }
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * 子が値だけのシーケンス配列（単一のスカラー値と同等として扱うため）
     */
    private function isSequenceArray(array $array)
    {
        // 空配列
        if ($array === []) {
            return true;
        }
        // 添字が連番ではない
        if (array_keys($array) !== range(0, count($array) - 1)) {
            return false;
        }
        // 値に配列を含む
        foreach($array as $key => $val) {
            if (is_array($val)) {
                return false;
            }
        }
        return true;
    }
}
