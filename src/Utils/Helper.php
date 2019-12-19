<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Utils;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Arr;
use Jmhc\Sms\Sms;
use Psr\Http\Message\RequestInterface;

/**
 * 辅助
 * @package Jmhc\Restful\Utils
 */
class Helper
{
    /**
     * 单例辅助
     * @param string $class
     * @param bool $refresh
     * @param array $params
     * @return mixed|null
     */
    public static function instance(string $class, bool $refresh = false, array $params = [])
    {
        $container = ApplicationContext::getContainer()->get(Container::class);
        $id = static::array2key($params, $class);
        if (! $container->has($id) || $refresh) {
            $container->instance($id, make($class, $params));
        }

        return $container->get($id);
    }

    /**
     * 获取 redis 缓存前缀
     * @param string $pool
     * @return string
     */
    public static function getRedisPrefix(string $pool = 'default')
    {
        $configInterface = ApplicationContext::getContainer()->get(ConfigInterface::class);
        return (string) $configInterface->get(
            sprintf('redis.%s.prefix', $pool),
            $configInterface->get('app_name', '')
        );
    }

    /**
     * 获取发送短信实例
     * @param string $pool
     * @return Sms
     */
    public static function getSms(string $pool = 'default')
    {
        $configInterface = ApplicationContext::getContainer()->get(ConfigInterface::class);
        return make(Sms::class, [
            'cache' => make(SmsCache::class, [
                'pool' => $pool,
            ]),
            'config' => $configInterface->get('sms', []),
        ]);
    }

    /**
     * 获取发送短信缓存
     * @param string $pool
     * @return \Jmhc\Sms\Utils\SmsCache
     */
    public static function getSmsCache(string $pool = 'default')
    {
        return make(\Jmhc\Sms\Utils\SmsCache::class, [
            'cache' => make(SmsCache::class, [
                'pool' => $pool,
            ]),
        ]);
    }

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
