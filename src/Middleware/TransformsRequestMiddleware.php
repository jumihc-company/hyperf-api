<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Middleware;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 转换请求参数中间件
 * @package Jmhc\Restful\Middleware
 */
class TransformsRequestMiddleware implements MiddlewareInterface
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
        $this->request->params = $this->cleanArray($this->request->params ?? []);

        return $handler->handle($request);
    }

    /**
     * 清除给定数组中的数据
     * @param array $data
     * @return array
     */
    protected function cleanArray(array $data)
    {
        return (new Collection($data))->map(function ($value, $key) {
            return $this->cleanValue($key, $value);
        })->all();
    }

    /**
     * 清除给定的值
     * @param string $key
     * @param $value
     * @return array
     */
    protected function cleanValue(string $key, $value)
    {
        if (is_array($value)) {
            return $this->cleanArray($value);
        }

        return $this->transform($key, $value);
    }

    /**
     * 转换给定的值
     * @param string $key
     * @param $value
     * @return mixed
     */
    protected function transform(string $key, $value)
    {
        return $value;
    }
}
