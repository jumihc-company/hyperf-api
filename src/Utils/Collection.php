<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Utils;

use Hyperf\Utils\HigherOrderCollectionProxy;

/**
 * 集合
 * @package Jmhc\Restful\Utils
 */
class Collection extends \Hyperf\Utils\Collection
{
    public function __get(string $key)
    {
        if (! in_array($key, static::$proxies)) {
            return $this->get($key);
        }
        return new HigherOrderCollectionProxy($this, $key);
    }

    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    public function __isset($name)
    {
        return $this->has($name);
    }

    public function __unset($name)
    {
        $this->offsetUnset($name);
    }
}
