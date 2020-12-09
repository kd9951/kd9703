<?php
namespace Kd9703\Framework\StrictInvokator;

use ReflectionClass;

/**
 * Usecase や MediaAccess のための __invoke の入出力チェックを厳格にするためのもの
 */
trait StrictInvokator
{
    use Concerns\ArrayFilterDefault2;
    use Concerns\Validate;
    use Concerns\CheckReturningValueType;

    protected function rules()
    {
        return [];
    }

    protected function messages()
    {
        return [];
    }

    protected function attributes()
    {
        return [];
    }

    protected function request_format()
    {
        return [];
    }

    protected function return_format()
    {
        return 'void';
    }

    /**
     * @param  $request
     * @return mixed
     */
    public function __invoke(array $request = [])
    {
        // // リクエストの配列構造を整える(イレギュラーな指定を排除)
        // $request = $this->ArrayFilterDefault($this->request_format(), $request);

        // // バリデーション
        // $this->validate($request, $this->rules(), $this->messages(), $this->attributes());

        $args  = [];
        $class = new ReflectionClass($this);
        foreach ($class->getMethod('exec')->getParameters() as $arg) {
            $name = $arg->name;
            if (array_key_exists($name, $request)) {
                $args[] = $request[$name];
            } elseif ($arg->isDefaultValueAvailable()) {
                $args[] = $arg->getDefaultValue();
            } else {
                throw new \LogicException("parameter $name required but missing at {$class->name}.");
            }
        }

        if ($this->systemLogger ?? null) {
            $m         = microtime(true);
            $classname = get_called_class();
            $this->systemLogger->debug("[EXEC] $classname@exec", $args);
        }

        // 実行
        $result = $this->exec(...$args);

        // // 型をチェック
        // $this->checkReturningValueType($result, $this->return_format());

        if ($this->systemLogger ?? null) {
            $classname = get_called_class();
            $sec       = number_format((microtime(true) - $m) * 1000);
            $this->systemLogger->debug("[DONE] $classname@exec {$sec}ms");
        }

        return $result;
    }

    // /**
    //  * @param array $request
    //  */
    // public function exec(array $request)
    // {
    //     return null;
    // }

}
