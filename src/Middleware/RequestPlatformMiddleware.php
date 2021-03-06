<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Middleware;

use Hyperf\HttpServer\Contract\RequestInterface;
use Jmhc\Restful\PlatformInfo;
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
     * @var RequestInterface
     */
    protected $request;

    public function __construct(
        RequestInterface $request
    )
    {
        $this->request = $request;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 请求平台
        $requestPlatform = $this->getRequestPlatform('request-platform');

        // 所有平台信息
        $allPlatform = PlatformInfo::getAllPlatform();

        // 请求平台
        $this->request->platform = PlatformInfo::OTHER;
        foreach ($allPlatform as $k => $v) {
            if (preg_match(sprintf('/(%s)/', $k), $requestPlatform)) {
                $this->request->platform = $v;
                break;
            }
        }

        return $handler->handle($request);
    }

    /**
     * 获取请求平台
     * @param string $name
     * @return array|string|null
     */
    protected function getRequestPlatform(string $name)
    {
        $platform = $this->request->header(ucwords($name, '-'));
        if (empty($platform)) {
            $platform = $this->request->input($name);
        }
        if(empty($platform)) {
            $platform = $this->request->server('HTTP_USER_AGENT');
        }

        return $platform;
    }
}
