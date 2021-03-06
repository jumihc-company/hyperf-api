<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Middleware;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 跨域中间件
 * @package Jmhc\Restful\Middleware
 */
class CorsMiddleware implements MiddlewareInterface
{
    /**
     * 跨域响应状态码
     * @var int
     */
    protected $statusCode = 204;

    /**
     * @var ConfigInterface
     */
    protected $configInterface;

    public function __construct(
        ConfigInterface $configInterface
    )
    {
        $this->configInterface = $configInterface;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = Context::get(ResponseInterface::class);
        foreach ($this->configInterface->get('jmhc-api.cors', []) as $k => $v) {
            $response = $response->withHeader($k, $v);
        }

        // 更新响应上下文
        Context::set(ResponseInterface::class, $response);

        if ($request->getMethod() == 'OPTIONS') {
            return $response->withStatus($this->statusCode);
        }

        return $handler->handle($request);
    }
}
