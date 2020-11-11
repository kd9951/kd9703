<?php
namespace Kd9703\Framework\StrictInvokator\Concerns;

/**
 * 与えられた配列を、指定されたフォーマットの配列に合わせる
 */
trait ArrayFilterDefault
{

    // http://arstropica.com/uncategorized/google-analytics-http-api/
    private function array_intersect_key_recursive(array $array1, array $array2) {
        $array1 = array_intersect_key($array1, $array2);
        foreach ($array1 as $key => &$value) {
            if (is_array($value) && is_array($array2[$key])) {
                $value = $this->array_intersect_key_recursive($value, $array2[$key]);
            }
        }
        return $array1;
    }

    // https://wpscholar.com/blog/filter-multidimensional-array-php/    
    private function array_filter_recursive( array $array, callable $callback = null ) {
        foreach ( $array as &$value ) {
            if ( is_array( $value ) ) {
                $value = $this->array_filter_recursive( $value, $callback );
            }
        }
        $array = is_callable( $callback ) ? array_filter( $array, $callback ) : array_filter( $array );
        return $array;
    }

    /**
     * 与えられた配列を、指定されたフォーマットの配列に合わせる
     */
    protected function ArrayFilterDefault(array $defaults, array $options)
    {
        return array_replace_recursive( 
            // NULLが撤去された後、与えられたNULLと空配列を復帰する
            $this->array_filter_recursive( 
                $options, 
                function($val) { return is_null($val) || is_array($val) || ($val===[]); }
            ),
            $this->array_filter_recursive( 
                array_replace_recursive( 
                    $defaults, 
                    $this->array_intersect_key_recursive(
                        $options, 
                        $defaults
                    )
                )
                // 結果からNULLと空配列を除去
                , function($val) { return !is_null($val) && ($val!==[]); }
            )
        );
    }

}
