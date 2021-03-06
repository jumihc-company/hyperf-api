<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Middleware;

use Hyperf\HttpServer\Contract\RequestInterface;
use Jmhc\Restful\Utils\Cipher;
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
     * @var RequestInterface
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
        RequestInterface $request,
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
        if ($this->request->has('params') && ! $jsonParams) {
            $params = $this->cipher->request($this->request->input('params'));
        }

        // 原请求参数
        $this->request->originParams = $params;

        // 过滤后参数
        $this->request->params = array_filter($params, function ($k) {
            return ! in_array($k, $this->filter);
        }, ARRAY_FILTER_USE_KEY);

        return $handler->handle($request);
    }
}
