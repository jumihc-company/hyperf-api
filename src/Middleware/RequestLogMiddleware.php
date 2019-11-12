<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Middleware;

use Jmhc\Restful\Utils\Helper;
use Jmhc\Restful\Utils\LogHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 请求日志中间件
 * @package Jmhc\Restful\Middleware
 */
class RequestLogMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 记录请求日志
        LogHelper::request()
            ->debug('', $this->buildContent($request));

        return $handler->handle($request);
    }

    /**
     * 生成消息
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function buildContent(ServerRequestInterface $request)
    {
        $ip = Helper::ip($request);
        $data = json_encode($request->all(), JSON_UNESCAPED_UNICODE);

        return <<<EOF
ip : {$ip}
referer : {$request->server('HTTP_REFERER', '-')}
user_agent : {$request->server('HTTP_USER_AGENT', '-')}
method : {$request->getMethod()}
url : {$request->fullUrl()}
data : {$data}
EOF;
    }
}
