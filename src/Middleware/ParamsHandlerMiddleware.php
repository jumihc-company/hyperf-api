<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Middleware;

use Hyperf\Di\Annotation\Inject;
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
     * 过滤键
     * @var array
     */
    protected $filter = ['sign', 'nonce', 'timestamp', 'file'];

    /**
     * @Inject()
     * @var Cipher
     */
    protected $cipher;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 是否直接存在json格式的params参数
        $jsonParams = json_decode($request->input('params', ''), true);

        // 请求参数
        $params = $jsonParams ?? $request->all();

        // 请求解密
        if ($request->exists('params') && ! $jsonParams) {
            $params = $this->cipher->request($request->input('params'));
        }

        // 原请求参数
        $request->originParams = $params;

        // 过滤后参数
        $request->params = array_filter($params, function ($k) {
            return ! in_array($k, $this->filter);
        }, ARRAY_FILTER_USE_KEY);

        // 更新请求上下文
        Context::set(RequestInterface::class, $request);

        return $handler->handle($request);
    }
}
