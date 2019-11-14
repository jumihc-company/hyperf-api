<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Middleware;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\HttpServer\Contract\RequestInterface;
use Jmhc\Restful\Contracts\UserModelInterface;
use Jmhc\Restful\Exceptions\ResultException;
use Jmhc\Restful\Models\UserModel;
use Jmhc\Restful\ResultCode;
use Jmhc\Restful\ResultMsg;
use Jmhc\Restful\Traits\ResultThrowTrait;
use Jmhc\Restful\Utils\Collection;
use Jmhc\Restful\Utils\Token;
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
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ConfigInterface
     */
    protected $configInterface;

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var UserModelInterface
     */
    protected $userModel;

    public function __construct(
        RequestInterface $request,
        ConfigInterface $configInterface,
        Token $token,
        UserModelInterface $userModel
    )
    {
        $this->request = $request;
        $this->configInterface = $configInterface;
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

        return $handler->handle($request);
    }

    /**
     * 验证
     * @return Builder|Model|object|null
     * @throws ResultException
     */
    protected function check()
    {
        // token
        $token = $this->token->get();

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
        $noticeTime = $this->token->getNoticeRefreshTime();
        if ((time() - $time) >= $noticeTime) {
            // 设置刷新的token
            $this->request->refreshToken = $this->token->create($id);
        }

        return $info;
    }
}
