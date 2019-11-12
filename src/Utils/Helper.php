<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Utils;

use Hyperf\Utils\Arr;
use Psr\Http\Message\RequestInterface;

/**
 * 辅助
 * @package Jmhc\Restful\Utils
 */
class Helper
{
    /**
     * 获取ip地址
     * @param RequestInterface $request
     * @return array|string|null
     */
    public static function ip(RequestInterface $request)
    {
        return $request->server('REMOTE_ADDR', '0.0.0.0');
    }

    /**
     * 获取url地址
     * @param $url
     * @param $value
     * @return string
     */
    protected static function getUrl($url, $value)
    {
        if (empty($value)) {
            return '';
        }

        if (preg_match('/^(http|https)/', $value)) {
            return $value;
        }

        return preg_replace('/\/*$/', '', $url) . '/' . preg_replace('/^\/*/', '', $value);
    }

    /**
     * 获取源路径
     * @param $url
     * @param $value
     * @return string
     */
    protected static function getOriginPath($url, $value)
    {
        return str_replace($url, '', $value);
    }

    /**
     * 转换成布尔值
     * @param $value
     * @return bool
     */
    public static function boolean($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        if ($value === 'true') {
            return true;
        }

        if ($value === 'false') {
            return false;
        }

        return !! $value;
    }

    /**
     * 数值转金钱
     * @param $value
     * @return float
     */
    public static function int2money($value)
    {
        return round($value / 100, 2);
    }

    /**
     * 金钱转数值
     * @param $value
     * @return int
     */
    public static function money2int($value)
    {
        return intval($value * 100);
    }

    /**
     * 数组转换成key
     * @param array $arr
     * @param string $flag
     * @return string
     */
    public static function array2key(array $arr, string $flag = '')
    {
        return md5(json_encode(Arr::sortRecursive($arr)) . $flag);
    }
}
