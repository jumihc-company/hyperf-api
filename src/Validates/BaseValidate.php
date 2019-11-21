<?php
/**
 * User: YL
 * Date: 2019/11/21
 */

namespace Jmhc\Restful\Validates;

use Hyperf\Contract\ValidatorInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class BaseValidate
{
    /**
     * @var ValidatorFactoryInterface
     */
    protected $validatorFactory;

    /**
     * 规则
     * @var array
     */
    protected $rules = [];

    /**
     * 消息
     * @var array
     */
    protected $messages = [];

    /**
     * 属性
     * @var array
     */
    protected $attributes = [];

    public function __construct(
        ValidatorFactoryInterface $validatorFactory
    )
    {
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * 验证
     * @param array $data
     * @return ValidatorInterface
     */
    public function check(array $data)
    {
        return $this->validatorFactory->make($data, $this->rules, $this->messages, $this->attributes);
    }

    /**
     * 选取需要的规则
     * @param array $fields
     * @return array
     */
    protected function only(array $fields)
    {
        return array_filter($this->rules, function ($key) use ($fields) {
            return $this->inArray($key, $fields);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * 移除需要的规则
     * @param array $fields
     * @return array
     */
    protected function remove(array $fields)
    {
        return array_filter($this->rules, function ($key) use ($fields) {
            return ! $this->inArray($key, $fields);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * 判断是否在字符串里
     * @param string $key
     * @param array $fields
     * @return bool
     */
    private function inArray(string $key, array $fields)
    {
        $res = in_array($key, $fields);
        if ($res) {
            return $res;
        }

        foreach ($fields as $field) {
            if (stripos($key, $field . '.') !== false) {
                $res = true;
                break;
            }
        }

        return $res;
    }
}
