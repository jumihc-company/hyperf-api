<?php
/**
 * User: YL
 * Date: 2019/11/18
 */

namespace Jmhc\Restful\Factory;

use Jmhc\Restful\Utils\Helper;
use ReflectionClass;
use ReflectionException;

/**
 * 工厂基类
 * @package Jmhc\Restful\Factory
 */
class BaseFactory
{
    /**
     * 文档注释
     * @var array
     */
    protected static $docComment;

    /**
     * 元数据
     * @var array
     */
    protected static $metadata;

    /**
     * 方法关键字
     * @var string
     */
    protected static $methodKeyword = ' * @method static ';

    public static function __callStatic($name, $arguments)
    {
        // 解析class
        $class = static::parse($name);
        if (empty($class)) {
            return null;
        }

        // 返回实例对象
        return Helper::instance($class, ! empty($arguments[0]), $arguments[1] ?? []);
    }

    /**
     * 获取对应类
     * @param string $method
     * @return mixed|string
     * @throws ReflectionException
     */
    private static function parse(string $method)
    {
        return static::getMetadata(static::getDocComment())[$method] ?? '';
    }

    /**
     * 获取文档注释
     * @return string
     * @throws ReflectionException
     */
    private static function getDocComment()
    {
        if (! isset(static::$docComment[static::class])) {
            static::$docComment[static::class] = (new ReflectionClass(new static()))->getDocComment();
        }

        return static::$docComment[static::class];
    }

    /**
     * 获取元数据
     * @param string $docComment
     * @return array
     */
    private static function getMetadata(string $docComment)
    {
        if (! isset(static::$metadata[static::class])) {
            static::$metadata[static::class] = static::createMetadata($docComment);
        }

        return static::$metadata[static::class];
    }

    /**
     * 创建元数据
     * @param string $docComment
     * @return array
     */
    private static function createMetadata(string $docComment)
    {
        $metadata = [];
        foreach (explode(PHP_EOL, $docComment) as $meta) {
            if (strpos($meta, static::$methodKeyword) === 0) {
                [$_class, $_method] = static::transform(trim(str_replace(static::$methodKeyword, '', $meta)));
                $metadata[$_method] = $_class;
            }
        }

        return $metadata;
    }

    /**
     * 转换
     * @param string $meta
     * @return array
     */
    private static function transform(string $meta)
    {
        $first = strpos($meta, ' ');
        $last = strpos($meta, '(');
        $start = $first + 1;

        return [
            mb_substr($meta, 0, $first),
            mb_substr($meta, $start, $last - $start)
        ];
    }
}
