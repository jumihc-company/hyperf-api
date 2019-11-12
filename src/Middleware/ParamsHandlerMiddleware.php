<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Middleware;

use Hyperf\Utils\Context;
use Jmhc\Restful\Utils\Cipher;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 请求参数处理中间件
 * @package Jmhc\Restful\Middleware
 */
class ParamsHandlerMiddleware implements MiddlewareInterface
{
    /**
     * @var \Hyperf\HttpServer\Contract\RequestInterface
     */
    protected $request;

    /**
     * @var Cipher
     */
    protected $cipher;

    /**
     * 过滤键
     * @var array
     */
    protected $filter = ['sign', 'nonce', 'timestamp', 'file'];

    public function __construct(
        \Hyperf\HttpServer\Contract\RequestInterface $request,
        Cipher $cipher
    )
    {
        $this->request = $request;
        $this->cipher = $cipher;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 是否直接存在json格式的params参数
        $jsonParams = json_decode($this->request->input('params', ''), true);

        // 请求参数
        $params = $jsonParams ?? $this->request->all();

        // 请求解密
        if ($this->request->exists('params') && ! $jsonParams) {
            $params = $this->cipher->request($this->request->input('params'));
        }

        // 原请求参数
        $this->request->originParams = $params;

        // 过滤后参数
        $this->request->params = array_filter($params, function ($k) {
            return ! in_array($k, $this->filter);
        }, ARRAY_FILTER_USE_KEY);

        // 更新请求上下文
        Context::set(RequestInterface::class, $this->request);

        return $handler->handle($this->request);
    }
}
