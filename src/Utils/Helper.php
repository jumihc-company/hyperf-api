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
        // 获取客户端ip
        $ip = $request->server('remote_addr', '');
        if (static::checkIp($ip)) {
            return $ip;
        }

        // 获取真实ip
        $ips = $request->getHeader('x_real_ip');
        if (! empty($ips[0]) && static::checkIp($ips[0])) {
            return $ips[0];
        }

        // 获取转发ip
        $ips = $request->getHeader('x_forwarded_for');
        if (! empty($ips[0]) && static::checkIp($ips[0])) {
            return $ips[0];
        }

        return '0.0.0.0';
    }

    /**
     * 检测ip
     * @param string $ip
     * @return bool
     */
    protected static function checkIp(string $ip)
    {
        return ! empty($ip) && ip2long($ip) !== false;
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
