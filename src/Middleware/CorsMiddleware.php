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
     * @var \Hyperf\HttpServer\Contract\ResponseInterface
     */
    protected $response;

    /**
     * @var ConfigInterface
     */
    protected $configInterface;

    public function __construct(
        \Hyperf\HttpServer\Contract\ResponseInterface $response,
        ConfigInterface $configInterface
    )
    {
        $this->response = $response;
        $this->configInterface = $configInterface;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        foreach ($this->configInterface->get('cors', []) as $k => $v) {
            $this->response = $this->response->withHeader($k, $v);
        }

        // 更新响应上下文
        Context::set(ResponseInterface::class, $this->response);

        if ($request->getMethod() == 'OPTIONS') {
            return $this->response;
        }

        return $handler->handle($request);
    }
}
