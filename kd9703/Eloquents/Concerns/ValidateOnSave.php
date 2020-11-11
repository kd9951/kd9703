<?php
namespace Kd9703\Eloquents\Concerns;

use Validator;

trait ValidateOnSave
{
    protected function rules()
    {
        return [
        ];
    }

    /**
     * @param array $options
     */
    public function save(array $options = [])
    {
        $rules = $this->rules();

        if (!empty($rules)) {
            // Validator::validate($this->attributes, $rules);
            $validator = \Validator::make($this->getDirty(), $rules);
            if ($validator->fails()) {
                throw new \LogicException(
                    "バリデーションに失敗"
                    . "\nERRS :" . var_export($validator->errors(), true)
                    . "\nATTRS:" . var_export($this->getDirty(), true)
                    . "\nRULES:" . var_export($rules, true)
                );
            }
        }

        parent::save($options);
    }
}
