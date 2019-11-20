<?php
/**
 * User: YL
 * Date: 2019/11/20
 */

namespace Jmhc\Restful\Utils;

use Closure;
use Hyperf\HttpServer\Router\Dispatched;
use Psr\Http\Message\ServerRequestInterface;

class Dispatch
{
    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * 类
     * @var string
     */
    protected $class = '';

    /**
     * 方法
     * @var string
     */
    protected $method = '';

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        $this->parseCallback();
    }

    /**
     * 获取类
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * 获取方法
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * 解析callback
     */
    protected function parseCallback()
    {
        // 获取当前调度回调
        $callback = $this->request->getAttribute(Dispatched::class)->handler->callback ?? [];

        // 如果是字符串
        if (is_string($callback)) {
            if (strpos($callback, '@') !== false) {
                $callback = explode('@', $callback);
            }
            if (strpos($callback, '::') !== false) {
                $callback = explode('::', $callback);
            }
        } elseif ($callback instanceof Closure) {
            // 匿名函数
            $callback = [];
        }

        $this->class = $callback[0] ?? '';
        $this->method = $callback[1] ?? '';
    }
}
