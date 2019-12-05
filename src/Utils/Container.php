<?php
/**
 * User: YL
 * Date: 2019/12/05
 */

namespace Jmhc\Restful\Utils;

use Psr\Container\ContainerInterface;

/**
 * 容器类
 * @package Jmhc\Restful\Utils
 */
class Container implements ContainerInterface
{
    /**
     * 单例容器
     * @var array
     */
    protected $instances = [];

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        return $this->instances[$id] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function has($id)
    {
        return ! empty($this->instances[$id]);
    }

    /**
     * 设置单例
     * @param $id
     * @param $instance
     * @return mixed
     */
    public function instance($id, $instance)
    {
        return $this->instances[$id] = $instance;
    }
}
