<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Utils;

use Hyperf\Redis\Redis;

/**
 * redis 锁
 * @package Jmhc\Restful\Utils
 */
class RedisLock
{
    /**
     * @var Redis|\Redis
     */
    protected $redis;

    /**
     * @var int
     */
    protected $seconds;

    /**
     * @var string
     */
    protected $key;

    public function __construct(Redis $redis, string $key, int $seconds)
    {
        $this->redis = $redis;
        $this->key = Helper::getRedisPrefix() . $key;
        $this->seconds = $seconds;
    }

    /**
     * 尝试获取锁
     * @param null $callback
     * @return bool
     */
    public function get($callback = null)
    {
        $result = $this->lock();
        if ($result && is_callable($callback)) {
            try {
                return $callback();
            } finally {
                $this->release();
            }
        }

        return $result;
    }

    /**
     * 锁定
     * @return bool
     */
    public function lock()
    {
        return $this->redis->set($this->key, 1, [
            'nx',
            'ex' => $this->seconds,
        ]);
    }

    /**
     * 释放
     * @return bool
     */
    public function release()
    {
        return !! $this->redis->del($this->key);
    }
}
