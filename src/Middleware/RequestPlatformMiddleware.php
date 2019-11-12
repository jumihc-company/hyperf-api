<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Middleware;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;
use Jmhc\Restful\PlatformInfo;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 请求平台中间件
 * @package Jmhc\Restful\Middleware
 */
class RequestPlatformMiddleware implements MiddlewareInterface
{
    /**
     * @Inject()
     * @var ConfigInterface
     */
    protected $configInterface;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 请求平台
        $requestPlatform = $this->getRequestPlatform(
            $request,
            $this->configInterface->get('jmhc-api.request_name.platform', 'request-platform')
        );

        // 所有 user_agent
        $allPlatform = PlatformInfo::getAllPlatform();

        // 请求平台
        $request->platform = PlatformInfo::OTHER;
        foreach ($allPlatform as $k => $v) {
            if (preg_match(sprintf('/(%s)/', $k), $requestPlatform)) {
                $request->platform = $v;
                break;
            }
        }

        // 更新请求上下文
        Context::set(RequestInterface::class, $request);

        return $handler->handle($request);
    }

    /**
     * 获取请求平台
     * @param ServerRequestInterface $request
     * @param string $name
     * @return array|string|null
     */
    protected function getRequestPlatform(ServerRequestInterface $request, string $name)
    {
        $platform = $request->header($name);
        if (empty($platform)) {
            $platform = $request->input($name);
        }
        if(empty($platform)) {
            $platform = $request->server('HTTP_USER_AGENT');
        }

        return $platform;
    }
}
