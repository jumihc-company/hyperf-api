<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Middleware;

use Hyperf\Di\Annotation\Inject;
use Jmhc\Restful\ResultCode;
use Jmhc\Restful\ResultMsg;
use Jmhc\Restful\Traits\ResultThrowTrait;
use Jmhc\Restful\Utils\SdlCache;
use Jmhc\Restful\Utils\Token;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 检测单设备登录中间件
 * @package Jmhc\Restful\Middleware
 */
class CheckSdlMiddleware implements MiddlewareInterface
{
    use ResultThrowTrait;

    /**
     * @Inject()
     * @var Token
     */
    protected $token;

    /**
     * @Inject()
     * @var SdlCache
     */
    protected $sdlCache;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $this->token->get();
        // token和用户id存在
        if (! empty($token) && ! empty($request->userInfo->id)) {
            if (! $this->sdlCache->verify($request->userInfo->id, $token)) {
                $this->error(ResultMsg::SDL, ResultCode::SDL);
            }
        }

        return $handler->handle($request);
    }
}
