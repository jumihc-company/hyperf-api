<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Middleware;

/**
 * 转换空字符串为 null
 * @package Jmhc\Restful\Middleware
 */
class ConvertEmptyStringsToNullMiddleware extends TransformsRequestMiddleware
{
    /**
     * 转换空字符串为null
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        return is_string($value) && $value === '' ? null : $value;
    }
}
