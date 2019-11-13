<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Middleware;

use Hyperf\HttpServer\Contract\RequestInterface;
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
        // 记录请求日志
        LogHelper::request()
            ->debug('', $this->buildContent());

        return $handler->handle($request);
    }

    /**
     * 生成消息
     * @return string
     */
    protected function buildContent()
    {
        $ip = Helper::ip($this->request);
        $data = json_encode($this->request->all(), JSON_UNESCAPED_UNICODE);

        return <<<EOF
ip : {$ip}
referer : {$this->request->server('HTTP_REFERER', '-')}
user_agent : {$this->request->server('HTTP_USER_AGENT', '-')}
method : {$this->request->getMethod()}
url : {$this->request->fullUrl()}
data : {$data}
EOF;
    }
}
