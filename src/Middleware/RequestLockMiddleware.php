<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Middleware;

use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Redis\Redis;
use Jmhc\Restful\ResultCode;
use Jmhc\Restful\Traits\ResultThrowTrait;
use Jmhc\Restful\Utils\Helper;
use Jmhc\Restful\Utils\RedisLock;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 请求锁定中间件
 * @package Jmhc\Restful\Middleware
 */
class RequestLockMiddleware implements MiddlewareInterface
{
    use ResultThrowTrait;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Redis|\Redis
     */
    protected $redis;

    /**
     * @var ConfigInterface
     */
    protected $configInterface;

    public function __construct(
        RequestInterface $request,
        Redis $redis,
        ConfigInterface $configInterface
    )
    {
        $this->request = $request;
        $this->redis = $redis;
        $this->configInterface = $configInterface;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 跨域请求
        if ($this->request->getMethod() === 'OPTIONS') {
            return $handler->handle($request);
        }

        // 请求锁定对象实例
        $this->request->requestLock = new RedisLock(
            $this->redis,
            $this->getLockKey(),
            $this->configInterface->get('jmhc-api.request_lock.seconds', 5)
        );
        if (! $this->request->requestLock->get()) {
            $this->error(
                $this->configInterface->get('jmhc-api.request_lock.tips', '请求已被锁定，请稍后重试~'),
                ResultCode::REQUEST_LOCKED
            );
        }

        return $handler->handle($request);
    }

    /**
     * 获取锁定标识
     * @return string|null
     */
    protected function getLockKey()
    {
        return 'lock-' . md5(Helper::ip($this->request) . $this->request->path() . json_encode($this->request->params));
    }
}
