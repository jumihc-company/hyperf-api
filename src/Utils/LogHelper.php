<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Utils;

use Hyperf\Utils\ApplicationContext;
use Throwable;

/**
 * 日志辅助
 * @package Jmhc\Restful\Utils
 */
class LogHelper
{
    /**
     * @var string
     */
    protected static $dir = 'storage/logs/%s';

    /**
     * 获取日志容器
     * @return Log
     */
    public static function get()
    {
        return ApplicationContext::getContainer()->get(Log::class);
    }

    /**
     * 异常保存
     * @param string $name
     * @param Throwable $e
     * @return mixed
     */
    public static function throwableSave(string $name, Throwable $e)
    {
        return static::get()->save(
            $name,
            $e->getMessage() . PHP_EOL . $e->getTraceAsString()
        );
    }

    /**
     * 请求日志
     * @param array $config
     * @return Log
     */
    public static function request(array $config = [])
    {
        return static::get()->setConfig(array_merge([
            'path' => static::dir('request'),
        ], $config));
    }

    /**
     * 保存路径
     * @param string $dir
     * @return string
     */
    protected static function dir(string $dir)
    {
        return sprintf(static::$dir, $dir);
    }
}
