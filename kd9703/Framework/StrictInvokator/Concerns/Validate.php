<?php
namespace Kd9703\Framework\StrictInvokator\Concerns;

use Illuminate\Validation\ValidationException;
use Validator;

/**
 * 与えられた配列を、指定されたルールでバリデーションしてエラーを返す
 */
trait Validate
{

    /**
     * 与えられた配列を、指定されたルールでバリデーションして例外を飛ばす
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validate(array $subject, array $rules, array $messages = [], array $attributes = [])
    {
        $validator = Validator::make($subject, $rules, $messages, $attributes);

        if ($validator->fails()) {
            if (app()->environment('testing') || app()->runningInConsole()) {
                // echo "\nFAILED\n\n";
                $this->echoErrors($subject, $rules, $validator->errors()->toArray());
            }
            // echo "\nENDED\n\n";
            throw new ValidationException($validator);
        }
    }

    /**
     * バリデーションエラーを良い感じにコンソール出力する
     *
     * @param  array  $rules
     * @param  array  $errors
     * @return void
     */
    private function echoErrors(array $subject, array $rules, array $errors)
    {
        $repack = [];

        // ルールごとのエラーメッセージに分類
        foreach ($rules as $attr => $rule) {
            $pattern = str_replace(['.', '[', ']', '*'], ['\.', '\[', '\]', '[^.]*'], $attr);
            foreach ($errors as $realattr => $messages) {
                if (preg_match("/^$pattern$/", $realattr)) {
                    foreach ($messages as $message) {
                        $repack[$attr][] = $message;
                    }
                }
            }
        }

        // 表示
        foreach ($repack as $attr => $messages) {
            echo "\n\n------------ ERROR\n";
            foreach ($messages as $message) {
                echo "$message\n";
            }

            // ルール表示
            $rule = $rules[$attr];
            echo "RULE : '$attr' => '$rule' \n";

            // 値表示：ドット記法なら1つ目まで
            $attr = explode('.', $attr)[0];
            echo "VALUE: '$attr' => " . (isset($subject[$attr]) ? var_export($subject[$attr], true) : 'undefined') . "\n";
        }
    }
}
