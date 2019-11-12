<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Middleware;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Jmhc\Restful\Exceptions\ResultException;
use Jmhc\Restful\Traits\ResultThrowTrait;
use Jmhc\Restful\Utils\Helper;
use Jmhc\Restful\Utils\Log;
use Jmhc\Restful\Utils\Signature;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Redis;

/**
 * 检测签名中间件
 * @package Jmhc\Restful\Middleware
 */
class CheckSignatureMiddleware implements MiddlewareInterface
{
    use ResultThrowTrait;

    /**
     * @Inject()
     * @var ConfigInterface
     */
    protected $configInterface;

    /**
     * @Inject()
     * @var Redis
     */
    protected $redis;

    /**
     * @Inject()
     * @var Log
     */
    protected $log;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (! $this->configInterface->get('jmhc-api.signature.check')) {
            return $handler->handle($request);
        }

        // 判断时间戳是否超时
        $timeout = $this->configInterface->get('jmhc-api.signature.timestamp_timeout', 60);
        $timestamp = $request->originParams['timestamp'] ?? 0;
        $time = time();
        if ($timestamp > ($time + $timeout) || ($timestamp + $timeout) < $time) {
            $this->error('请求已过期~');
        }

        // 判断随机数是否有效
        $nonce = $request->originParams['nonce'] ?? '';
        $this->validateNonce($nonce, $timestamp, $timeout);

        // 验证签名是否正确
        $sign = $request->originParams['sign'] ?? '';
        $data = $request->params ?? [];
        $data['timestamp'] = $timestamp;
        $data['nonce'] = $nonce;
        $key = $this->configInterface->get('jmhc-api.signature.key', '');
        if (! Signature::verify($sign, $data, $key)) {
            // 签名验证失败记录
            $this->log->save(
                'signature.error',
                $this->getSignatureErrorMsg($request, $sign, $data, $key)
            );
            $this->error('签名验证失败~');
        }

        return $handler->handle($request);
    }

    /**
     * 验证随机数
     * @param string $nonce
     * @param int $timestamp
     * @param int $timeout
     * @throws ResultException
     */
    protected function validateNonce(string $nonce, int $timestamp, int $timeout)
    {
        if (empty($nonce)) {
            $this->error('请求随机数不存在~');
        }

        // 保存的随机数
        $nonce .= $timestamp;

        // 缓存标识
        $cacheKey = 'nonce-list-' . $timeout;

        // 获取已缓存随机数列表
        $list = $this->redis->lrange($cacheKey, 0, -1);
        if (in_array($nonce, $list)) {
            $this->error('请求随机数已存在~');
        }

        // 添加随机数
        $this->redis->lpush($cacheKey, $nonce);

        // 设置过期时间
        if ($this->redis->ttl($cacheKey) == -1) {
            $this->redis->expire($cacheKey, $timeout);
        }
    }

    /**
     * 获取签名错误消息
     * @param ServerRequestInterface $request
     * @param string $sign
     * @param array $data
     * @param string $key
     * @return string
     */
    protected function getSignatureErrorMsg(ServerRequestInterface $request, string $sign, array $data, string $key)
    {
        // 签名数据
        $signData = Signature::sign($data, $key, false);
        $origin = json_encode($signData['origin'], JSON_UNESCAPED_UNICODE);
        $sort = json_encode($signData['sort'], JSON_UNESCAPED_UNICODE);

        // 获取ip
        $ip = Helper::ip($request);

        return <<<EOL
ip: {$ip}
url: {$request->fullUrl()}
method: {$request->method()}
源数据: {$origin}
排序后数据: {$sort}
构造签名字符串: {$signData['build']}
待签名数据: {$signData['wait_str']}
签名结果: {$signData['sign']}
请求签名: {$sign}
EOL;
    }
}
