<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Middleware;

use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\Utils\Context;
use Jmhc\Restful\Contracts\UserInterface;
use Jmhc\Restful\Exceptions\ResultException;
use Jmhc\Restful\Models\UserModel;
use Jmhc\Restful\ResultCode;
use Jmhc\Restful\ResultMsg;
use Jmhc\Restful\Traits\ResultThrowTrait;
use Jmhc\Restful\Utils\Collection;
use Jmhc\Restful\Utils\Token;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 检测令牌中间件
 * @package Jmhc\Restful\Middleware
 */
class CheckTokenMiddleware implements MiddlewareInterface
{
    use ResultThrowTrait;

    /**
     * @var \Hyperf\HttpServer\Contract\RequestInterface
     */
    protected $request;

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var UserInterface
     */
    protected $userModel;

    public function __construct(
        \Hyperf\HttpServer\Contract\RequestInterface $request,
        Token $token,
        UserInterface $userModel
    )
    {
        $this->request = $request;
        $this->token = $token;
        $this->userModel = $userModel;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            // 用户信息
            $this->request->userInfo = $this->check();
        } catch (ResultException $e) {
            // 强制登录 TODO: 中间件参数
            if (true) {
                throw $e;
            }

            // 用户信息
            $this->request->userInfo = new Collection();
        }

        // 更新请求上下文
        Context::set(RequestInterface::class, $this->request);

        return $handler->handle($this->request);
    }

    /**
     * 验证
     * @return Builder|Model|object|null
     * @throws ResultException
     */
    protected function check()
    {
        // token
        $token = $this->token->get('token');

        // 判断token是否存在
        if (empty($token)) {
            $this->error(ResultMsg::TOKEN_NO_EXISTS, ResultCode::TOKEN_NO_EXISTS);
        }

        // 解析token
        $parse = $this->token->parse($token);

        // 验证token
        $verify = $this->token->verify($parse);
        if ($verify !== true) {
            [$code, $msg] = $verify;
            $this->error($msg, $code);
        }

        // 解析[加密字符, 加密时间]
        [$id, $time] = $parse;

        // 判断token是否有效
        $info = $this->userModel->getInfoById($id);
        if (empty($info)) {
            $this->error(ResultMsg::TOKEN_INVALID, ResultCode::TOKEN_INVALID);
        } elseif ($info->status != UserModel::YES) {
            $this->error(ResultMsg::PROHIBIT_LOGIN, ResultCode::PROHIBIT_LOGIN);
        }

        // 判断是否刷新token
        $noticeTime = $this->configInterface->get('jmhc-api.token.notice_refresh_time', 0);
        if ((time() - $time) >= $noticeTime) {
            // 设置刷新的token
            $this->request->refreshToken = $this->token->create($id);
        }

        return $info;
    }
}
