<?php

namespace Kd9703\Entities;

use DateTime;
use Kd9703\Constants\Enum;
use Kd9703\Entities\ValueObjects\ValueObject;

trait EntityCast
{
    /**
     * Entityの要素に指定された「型」から実際に入れられる値を検査し、適合した型にして返す
     * @param array $keyvalues
     */
    protected function _checkAndCast(string $name, string $definication, $value, $current_value)
    {
        $definication = (trim(preg_replace('/\s+/', ' ', $definication)));

        if (strpos(strtolower($definication), 'array of') === 0) {
            $required_type        = 'array';
            $required_of_type     = substr($definication, 9);
            $required_type_option = null;
        } else {
            $splitted             = explode(':', $definication, 2);
            $required_type        = trim($splitted[0]);
            $required_type_option = trim($splitted[1] ?? '');
            $required_of_type     = null;
        }

        // NULLABLE
        // NULL以外の値からNULLに戻すことを「許可する」
        if (strpos($required_type, '?') === 0) {
            // null ならそれで終了
            if (is_null($value)) {
                return $value;
            }
            // 違うなら？を取って処理続行
            $required_type = substr($required_type, 1);
        }
        // （初期値として）NULLがセットされているならNULLセットを認める（「許可する」ではなくて「無視する」）
        // FIXME $current_valueを引数として受けているので「NULLセットされているか、未セットなら」になっている 未セット→NULLになってしまう
        if (is_null($value) && is_null($current_value)) {
            return $value;
        }

        $actual_type = gettype($value);

        // FIXME これをやらないと tinker で誤判定される
        is_a('Kd9703\Entities\ValueObjects\EncryptedPassword', 'Kd9703\Entities\ValueObjects\ValueObject', true);
        is_a('Kd9703\Entities\ValueObjects\HashedPassword', 'Kd9703\Entities\ValueObjects\ValueObject', true);
        is_a('Kd9703\Entities\Media\Post', 'Kd9703\Entities\Entity', true);

        // PHPより厳しめのキャスト
        switch (strtolower($required_type)) {
            case 'bool':
            case 'boolean':
                // 'false'という文字列や配列をBOOLにするのが予期した結果になる保証がない
                // のだがLaravelアプリと割り切れば、利便性のが高い
                if (in_array($value, ['true', 'on', 'yes'], true)) {
                    return true;
                }
                if (in_array($value, ['false', 'off', 'no'], true)) {
                    return false;
                }
                if (in_array($actual_type, ['boolean', 'integer', 'string'])) {
                    if (!is_bool($value) && !is_numeric($value)) {
                        throw new \LogicException("required type of '$name' is 'bool'. Given value could not be cast to boolean. " . var_export($value, true));
                    }
                    return $value ? true : false;
                }
                break;

            case 'int':
            case 'integer':
                // 'ABC' といった文字列がゼロにキャストされることがあるので
                if (!is_numeric($value)) {
                    throw new \LogicException("required type of '$name' is 'integer'. Given value could not be cast to integer. " . var_export($value, true));
                }
                return (int) $value;

            case 'float':
                throw new \LogicException("required type of '$name' 'float' forbidden. use 'double'. (ambiguous).");

            case 'double':
                if (!is_numeric($value)) {
                    throw new \LogicException("required type of '$name' is 'double'. Given value could not be cast to double. " . var_export($value, true));
                }
                return (float) $value;

            case 'string':
                if (in_array($actual_type, ['string', 'integer', 'double'])) {
                    return (string) $value;
                }
                break;

            case 'date':
                // DateTimeなら文字列にキャスト
                if (is_a($value, DateTime::class, true)) {
                    $required_type_option = $required_type_option ?? 'Y-m-d H:i:s';

                    return $value->format($required_type_option);
                }
                // integerならtimestampとみなして文字列にキャスト（余計なお世話かも）
                if (in_array($actual_type, ['integer'])) {
                    $required_type_option = $required_type_option ?? 'Y-m-d H:i:s';

                    return date($required_type_option, $value);
                }
                // : オプションがあればフォーマットチェック
                if ($required_type_option) {
                    $dt = DateTime::createFromFormat($required_type_option, $value);
                    if ($dt === false || array_sum($dt::getLastErrors())) {
                        throw new \LogicException("'$name' must be '$required_type_option'. " . var_export($value, true));
                    }

                    return (string) $value;
                } else {
                    $required_type_option = $required_type_option ?: 'Y-m-d H:i:s';

                    return date($required_type_option, strtotime($value));
                }
                break;

            case 'array':
                if (!is_array($value)) {
                    throw new \LogicException("type of '$name' requires array but $actual_type given . " . var_export($value, true));
                }

                if ($required_of_type) {
                    foreach ($value as $idx => $item) {
                        $value[$idx] = $this->_checkAndCast("$name.$idx", $required_of_type, $item, $current_value[$idx] ?? null);
                    }
                }
                return $value;

            case 'object':
                throw new \LogicException("type of '$name' 'object' forbidden. use 'array' or specified class name. (ambiguous).");

            default:
                // var_dump($name, $required_type, $value, is_a($required_type, Entity::class, true), is_a($definication, Entity::class, true));
                // Enumならインスタンス生成
                if (is_a($required_type, Enum::class, true)) {
                    if (is_a($value, $required_type, true)) {
                        return $value;
                    }
                    return new $required_type($value);
                }
                // ValueObjectならインスタンス生成
                if (is_a($required_type, ValueObject::class, true)) {
                    if (is_a($value, $required_type, true)) {
                        return $value;
                    }
                    return new $required_type($value);
                }
                // EntityListならインスタンス生成
                if (is_a($required_type, EntityList::class, true)) {
                    if (is_a($value, $required_type, true)) {
                        return $value;
                    }
                    if (is_array($value)) {
                        return new $required_type($value);
                    }
                    throw new \LogicException("type of '$name' requires EntityList or array but $actual_type given . " . var_export($value, true));
                }
                // Entityならインスタンス生成
                if (is_a($required_type, Entity::class, true)) {
                    if (is_a($value, $required_type, true)) {
                        return $value;
                    }
                    if (is_array($value)) {
                        return new $required_type($value);
                    }
                    throw new \LogicException("type of '$name' requires Entity $required_type or array but $actual_type given . " . var_export($value, true));
                }
                // クラスなら型一致 is_a キャストしない
                if (is_a($value, $required_type, true)) {
                    return $value;
                }
        }

        throw new \LogicException("type of property $name expects $definication ($required_type, $required_of_type) but $actual_type given.");
    }
}
