<?php
/**
 * User: YL
 * Date: 2019/12/19
 */

namespace Jmhc\Restful\Utils;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Redis\Redis;
use Jmhc\Sms\Contracts\CacheInterface;

class SmsCache implements CacheInterface
{
    /**
     * @var Redis|\Redis
     */
    protected $redis;

    /**
     * @var ConfigInterface
     */
    protected $configInterface;

    /**
     * @var string
     */
    protected $prefix;

    public function __construct(Redis $redis, ConfigInterface $configInterface, string $pool = 'default')
    {
        $this->redis = $redis;
        $this->configInterface = $configInterface;
        $this->prefix = Helper::getRedisPrefix($pool);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key): array
    {
        return $this->redis->hGetAll($this->getCacheKey($key));
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, array $data): bool
    {
        return $this->redis->hMSet($this->getCacheKey($key), $data);
    }

    /**
     * @inheritDoc
     */
    public function expire(string $key, int $ttl): bool
    {
        return $this->redis->expire($this->getCacheKey($key), $ttl);
    }

    /**
     * @inheritDoc
     */
    public function exists(string $key): bool
    {
        return !! $this->redis->exists($this->getCacheKey($key));
    }

    /**
     * @inheritDoc
     */
    public function del(string $key): bool
    {
        return !! $this->redis->del($this->getCacheKey($key));
    }

    /**
     * @inheritDoc
     */
    public function lock(string $key): bool
    {
        return $this->redis->set($key, 1, [
            'nx',
            'ex' => $this->configInterface->get('jmhc-api.sms_send_lock_seconds', 5),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function unlock(string $key): bool
    {
        return $this->del($key);
    }

    /**
     * 获取缓存key
     * @param $key
     * @return string
     */
    protected function getCacheKey($key)
    {
        return $this->prefix . $key;
    }
}
