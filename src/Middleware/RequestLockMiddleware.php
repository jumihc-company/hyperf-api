<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Middleware;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;
use Jmhc\Restful\ResultCode;
use Jmhc\Restful\Traits\ResultThrowTrait;
use Jmhc\Restful\Utils\Helper;
use Jmhc\Restful\Utils\RedisLock;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Redis;

/**
 * 请求锁定中间件
 * @package Jmhc\Restful\Middleware
 */
class RequestLockMiddleware implements MiddlewareInterface
{
    use ResultThrowTrait;

    /**
     * @Inject()
     * @var Redis
     */
    protected $redis;

    /**
     * @Inject()
     * @var ConfigInterface
     */
    protected $configInterface;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 跨域请求
        if ($request->getMethod() === 'OPTIONS') {
            return $handler->handle($request);
        }

        // 请求锁定对象实例
        $request->requestLock = new RedisLock(
            $this->redis,
            $this->getLockKey($request),
            $this->configInterface->get('jmhc-api.request_lock.seconds', 5)
        );
        if (! $request->requestLock->get()) {
            $this->error(
                $this->configInterface->get('jmhc-api.request_lock.tips', '请求已被锁定，请稍后重试~'),
                ResultCode::REQUEST_LOCKED
            );
        }

        // 更新请求上下文
        Context::set(RequestInterface::class, $request);

        return $handler->handle($request);
    }

    /**
     * 获取锁定标识
     * @param ServerRequestInterface $request
     * @return string|null
     */
    protected function getLockKey(ServerRequestInterface $request)
    {
        return 'lock-' . md5(Helper::ip($request) . $request->path() . json_encode($request->params));
    }
}
