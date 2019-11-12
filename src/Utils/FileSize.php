<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Utils;

use InvalidArgumentException;

/**
 * 文件大小
 * @package Jmhc\Restful\Utils
 */
class FileSize
{
    const MATCH_NUM = 3;

    protected static $pattern = '/^(\d+)([a-z]+)$/';

    protected static $str = 'bkmgtp';
    protected static $strMapping = [
        'kb' => 'k',
        'mb' => 'm',
        'gb' => 'g',
        'tb' => 't',
        'pb' => 'p',
    ];

    /**
     * 获取尺寸(字节)
     * @param $size
     * @return int
     */
    public static function get($size)
    {
        if (filter_var($size, FILTER_VALIDATE_INT)) {
            return $size;
        } elseif (! is_string($size)) {
            throw new InvalidArgumentException('size must be a string or number');
        }

        preg_match(static::$pattern, mb_strtolower($size), $match);
        if (count($match) != static::MATCH_NUM) {
            throw new InvalidArgumentException('size format is not correct');
        }

        [, $num, $unit] = $match;
        $pos = stripos(static::$str, $unit);
        if ($pos === false) {
            if (! empty(static::$strMapping[$unit])) {
                $pos = stripos(static::$str, static::$strMapping[$unit]);
            } else {
                throw new InvalidArgumentException('size unit is incorrect');
            }
        }

        return intval($num * pow(1024, $pos));
    }
}
