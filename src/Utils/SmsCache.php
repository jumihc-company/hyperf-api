<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Utils;

use Hyperf\Redis\Redis;

/**
 * 短信缓存
 * @package Jmhc\Restful\Utils
 */
class SmsCache
{
    /**
     * @var Redis|\Redis
     */
    protected $redis;

    /**
     * redis 缓存前缀
     * @var string
     */
    protected $redisPrefix;

    /**
     * @var string
     */
    protected $codeCacheKey = 'sms_code_cache_%s';

    /**
     * @var string
     */
    protected $numCacheKey = 'sms_num_cache_%s';

    /**
     * code保存格式
     * @var array
     */
    protected $codeFormat = [
        'code' => 0,
        'time' => 0,
    ];

    /**
     * num保存格式
     * @var array
     */
    protected $numFormat = [
        'num' => 0,
        'time' => 0,
    ];

    /**
     * 发送间隔时间
     * @var array
     */
    protected $interval = [
        1 => 60,
        2 => 180,
        3 => 600,
    ];

    /**
     * 有效期(秒)
     * @var int
     */
    protected $validTime = 1800;

    public function __construct(
        Redis $redis,
        array $interval = [],
        int $validTime = 0
    )
    {
        $this->redis = $redis;
        $this->redisPrefix = Helper::getRedisPrefix();

        if (! empty($interval)) {
            $this->interval = $interval;
        }

        if (! empty($validTime)) {
            $this->validTime = $validTime;
        }
    }

    /**
     * 获取有效期
     * @return int
     */
    public function getValidTime()
    {
        return $this->validTime;
    }

    /**
     * 设置验证码
     * @param string $phone
     * @param string $code
     * @param string $type
     */
    public function setCode(string $phone, string $code, string $type = '')
    {
        $time = time();
        // 设置code缓存
        $this->setCodeCache($phone, $code, $type, $time);
        // 设置次数缓存
        $this->setNumCache($phone, $type, $time);
    }

    /**
     * 使用验证码
     * @param string $phone
     * @param string $type
     * @return bool
     */
    public function useCode(string $phone, string $type = '')
    {
        $key = $this->getCodeCacheKey($phone, $type);

        if (! $this->exists($key)) {
            return true;
        }

        $data = $this->getCodeCache($phone, $type);
        if (! empty($data['code'])) {
            $this->redis->del($key);
        }

        return true;
    }

    /**
     * 验证
     * @param string $phone
     * @param string $code
     * @param string $type
     * @return bool|string
     */
    public function verify(string $phone, string $code, string $type = '')
    {
        $data = $this->getCodeCache($phone, $type);

        if (empty($data['code'])) {
            return '无效的验证码';
        } elseif ($data['code'] != $code) {
            return '验证码不正确';
        }

        return true;
    }

    /**
     * 发送
     * @param string $phone
     * @param string $type
     * @return bool|int|mixed
     */
    public function send(string $phone, string $type = '')
    {
        $data = $this->getNumCache($phone, $type);
        $diff = time() - $data['time'];
        $getInterval = $this->getInterval($data['num']);
        if ($getInterval > $diff) {
            return $getInterval - $diff;
        }

        return true;
    }

    /**
     * 发送间隔
     * @param string $phone
     * @param string $type
     * @return int|mixed
     */
    public function sendInterval(string $phone, string $type = '')
    {
        $data = $this->getNumCache($phone, $type);
        return $this->getInterval($data['num']);
    }

    /**
     * 获取数据
     * @param string $phone
     * @param string $type
     * @return array
     */
    protected function getCodeCache(string $phone, string $type = '')
    {
        $data = $this->redis->hgetall($this->getCodeCacheKey($phone, $type));
        return ! empty($data) ? $data : $this->codeFormat;
    }

    /**
     * 获取发送数量
     * @param string $phone
     * @param string $type
     * @return array
     */
    protected function getNumCache(string $phone, string $type = '')
    {
        $data = $this->redis->hgetall($this->getNumCacheKey($phone, $type));
        return ! empty($data) ? $data : $this->numFormat;
    }

    /**
     * 判断是否存在
     * @param string $key
     * @return bool
     */
    protected function exists(string $key)
    {
        return !! $this->redis->exists($key);
    }

    /**
     * 获取缓存key
     * @param string $phone
     * @param string $type
     * @return string
     */
    protected function getCodeCacheKey(string $phone, string $type)
    {
        return $this->redisPrefix . sprintf(
            $this->codeCacheKey,
            ! empty($type) ? $type . '_' . $phone : $phone
        );
    }

    /**
     * 获取发送次数缓存
     * @param string $phone
     * @param string $type
     * @return string
     */
    protected function getNumCacheKey(string $phone, string $type)
    {
        return $this->redisPrefix . sprintf(
            $this->numCacheKey,
            ! empty($type) ? $type . '_' . $phone : $phone
        );
    }

    /**
     * 设置code缓存
     * @param string $phone
     * @param string $code
     * @param string $type
     * @param int $time
     * @return mixed
     */
    protected function setCodeCache(string $phone, string $code, string $type, int $time)
    {
        // 发送验证码
        $key = $this->getCodeCacheKey($phone, $type);
        $res = $this->redis->hmset($key, [
            'code' => $code,
            'time' => $time,
        ]);

        // 设置验证码过期时间
        if ($res) {
            $this->redis->expire($key, $this->getValidTime());
        }

        return $res;
    }

    /**
     * 设置次数缓存
     * @param string $phone
     * @param string $type
     * @param int $time
     * @return mixed
     */
    protected function setNumCache(string $phone, string $type, int $time)
    {
        $key = $this->getNumCacheKey($phone, $type);
        $exists = $this->exists($key);

        // 保存数据
        $data = $this->getNumCache($phone, $type);
        $data['num']++;
        $data['time'] = $time;

        // 设置发送次数
        $res = $this->redis->hmset($key, $data);

        // 不存在时设置缓存
        if (! $exists && $res) {
            $this->redis->expire($key, $this->getExpireTime());
        }

        return $res;
    }

    /**
     * 获取过期时间
     * @return false|int
     */
    protected function getExpireTime()
    {
        return strtotime(date('Y-m-d 00:00:00', strtotime('+1 day'))) - time();
    }

    /**
     * 获取间隔时间
     * @param int $num
     * @return int|mixed
     */
    protected function getInterval(int $num)
    {
        // 存在的话
        if (! empty($this->interval[$num])) {
            return $this->interval[$num];
        }

        // 判断最大值
        if ($num >= array_search(max($this->interval), $this->interval)) {
            return  max($this->interval);
        }

        $interval = 60;
        foreach ($this->interval as $k => $v) {
            if ($k > $num) {
                break;
            }
            $interval = $v;
        }
        return $interval;
    }
}
