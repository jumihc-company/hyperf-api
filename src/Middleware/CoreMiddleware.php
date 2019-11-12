<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Middleware;

use Closure;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Router\Dispatched;
use Jmhc\Restful\Exceptions\InvalidRequestException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * 核心中间件
 * @package Jmhc\Restful\Middleware
 */
class CoreMiddleware extends \Hyperf\HttpServer\CoreMiddleware
{
    protected function handleFound(Dispatched $dispatched, ServerRequestInterface $request)
    {
        if ($dispatched->handler->callback instanceof Closure) {
            $response = call($dispatched->handler->callback);
        } else {
            [$controller, $action] = $this->prepareHandler($dispatched->handler->callback);
            $controllerInstance = $this->container->get($controller);
            if (! method_exists($controller, $action)) {
                // Route found, but the handler does not exist.
                return $this->response()->withStatus(500)->withBody(new SwooleStream('Method of class does not exist.'));
            }
            $parameters = $this->parseParameters($controller, $action, $dispatched->params);
            $this->updateAttribute($controllerInstance);
            $response = $controllerInstance->{$action}(...$parameters);
        }
        return $response;
    }

    protected function handleNotFound(ServerRequestInterface $request)
    {
        throw new InvalidRequestException();
    }

    protected function handleMethodNotAllowed(array $methods, ServerRequestInterface $request)
    {
        throw new InvalidRequestException();
    }

    protected function updateAttribute($controllerInstance)
    {
        // 更新控制器属性
        method_exists($controllerInstance, 'setRequestInfo') && $controllerInstance->setRequestInfo();
        method_exists($controllerInstance, 'setUserInfo') && $controllerInstance->setUserInfo();

        // 更新服务属性
        if (property_exists($controllerInstance, 'service')) {
            method_exists($controllerInstance->service, 'setRequestInfo') && $controllerInstance->service->setRequestInfo();
            method_exists($controllerInstance->service, 'setUserInfo') && $controllerInstance->service->setUserInfo();
        }
    }
}
