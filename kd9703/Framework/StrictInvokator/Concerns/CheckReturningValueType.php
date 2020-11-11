<?php
namespace Kd9703\Framework\StrictInvokator\Concerns;

/**
 * 与えられた配列を、指定されたルールでバリデーションしてエラーを返す
 * TODO チェックするのは開発環境だけでいいので本番ではスルーするように
 */
trait CheckReturningValueType
{
    protected function return_type()
    {
        return $this->return_type;
    }

    /**
     * 与えられた配列を、指定されたルールでバリデーションして例外を飛ばす
     * 
     * @throws App\Usecases\TypeOfReturningValueInvalidException
     */
    protected function checkReturningValueType($result)
    {
        if( $this->return_type() == "" ){
            throw new \App\Usecases\TypeOfReturningValueInvalidException("return type not defined on ".get_called_class());
        }

        // 大文字小文字は区別しない
        $result_type = strtolower(gettype($result));
        $expect_type = strtolower($this->return_type());

        // 型名一致
        if( $result_type === $expect_type ){
            return true;
        }
        // クラス名一致(大文字小文字は区別しない PHPの仕様に合わせて)
        if( $result_type === 'object' && strtolower(get_class( $result )) === $expect_type ){
            return true;
        }
        // gettypeに未定義だけど指定可能な型
        switch( $expect_type ){
            case 'null':
            case 'void':
                if( $result_type === 'null' ){
                    return true;
                }
                break;
            case 'int':
                if( $result_type === 'integer' ){
                    return true;
                }
                break;
            case 'bool':
                if( $result_type === 'boolean' ){
                    return true;
                }
                break;
            case 'fload':
                if( $result_type === 'double' ){
                    return true;
                }
                break;
            case 'mixed':
                return true;
        }
        
        throw new \App\Usecases\TypeOfReturningValueInvalidException("return type mismatched. expects:$expect_type actural:$result_type on ".get_called_class());
    }

}
