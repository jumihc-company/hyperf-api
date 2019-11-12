<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Utils;

/**
 * 签名
 * @package Jmhc\Restful\Utils
 */
class Signature
{
    /**
     * 签名数据
     * @param array $data
     * @param string $key
     * @param bool $isOnlySign
     * @return string
     */
    public static function sign(array $data, string $key, bool $isOnlySign = true)
    {
        $res['origin'] = $data;

        // 排序数据
        ksort($data);
        $res['sort'] = $data;

        // 构造签名字符串
        $res['build'] = http_build_query($res['sort'], null, '&', PHP_QUERY_RFC3986);

        // 待签名字符串
        $res['wait_str'] = $key . $res['build'] . $key;

        // 签名
        $res['sign'] = md5($res['wait_str']);

        return $isOnlySign ? $res['sign'] : $res;
    }

    /**
     * 验证签名
     * @param string $sign
     * @param array $data
     * @param string $key
     * @return bool
     */
    public static function verify(string $sign, array $data, string $key)
    {
        return $sign === static::sign($data, $key);
    }
}
