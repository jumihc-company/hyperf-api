<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Utils;

use Hyperf\Contract\ConfigInterface;
use Redis;

/**
 * 单设备缓存
 * @package Jmhc\Restful\Utils
 */
class SdlCache
{
    /**
     * @var ConfigInterface
     */
    protected $configInterface;

    /**
     * @var Redis
     */
    protected $redis;

    /**
     * 缓存key
     * @var string
     */
    protected $cacheKey = 'sdl_%d';

    /**
     * 缓存临时key
     * @var string
     */
    protected $cacheTempKey = 'sdl_temp_%d';

    public function __construct(
        ConfigInterface $configInterface,
        Redis $redis
    )
    {
        $this->configInterface = $configInterface;
        $this->redis = $redis;
    }

    /**
     * 获取缓存数据
     * @param int $id
     * @return array
     */
    public function get(int $id)
    {
        return array_filter([
            $this->redis->get($this->getCacheKey($id)),
            $this->redis->get($this->getCacheTempKey($id)),
        ]);
    }

    /**
     * 设置缓存数据
     * @param int $id
     * @param string $token
     * @param string $oldToken
     * @return bool
     */
    public function set(int $id, string $token, string $oldToken = '')
    {
        $this->redis->set($this->getCacheKey($id), $token);

        // 旧token存在
        if (! empty($oldToken)) {
            $this->redis->setex(
                $this->getCacheTempKey($id),
                $this->configInterface->get('jmhc-api.sdl_tmp_expire', 10),
                $oldToken
            );
        }

        return true;
    }

    /**
     * 验证是否通过
     * @param int $id
     * @param string $token
     * @return bool
     */
    public function verify(int $id, string $token)
    {
        return in_array($token, $this->get($id));
    }

    /**
     * 获取缓存key
     * @param int $id
     * @return string
     */
    protected function getCacheKey(int $id)
    {
        return sprintf($this->cacheKey, $id);
    }

    /**
     * 获取临时key
     * @param int $id
     * @return string
     */
    protected function getCacheTempKey(int $id)
    {
        return sprintf($this->cacheTempKey, $id);
    }
}
