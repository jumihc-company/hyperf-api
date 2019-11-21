<?php
/**
 * User: YL
 * Date: 2019/11/21
 */

namespace Jmhc\Restful\Contracts;

/**
 * 自定义验证规则
 * @package Jmhc\Restful\Contracts
 */
interface RuleInterface
{
    /**
     * 操作
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return bool
     */
    public function handle($attribute, $value, $parameters, $validator): bool;

    /**
     * 验证消息
     * @return string
     */
    public function message(): string;
}
