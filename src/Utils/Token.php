<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Utils;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\Str;
use Jmhc\Restful\ResultCode;
use Jmhc\Restful\ResultMsg;
use Jmhc\Restful\Utils\Cipher\Token as TokenCipher;

/**
 * 令牌加密
 * @package Jmhc\Restful\Utils
 */
class Token
{
    /**
     * @Inject()
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject()
     * @var TokenCipher
     */
    protected $core;

    /**
     * 获取token
     * @param string $name
     * @return string
     */
    public function get(string $name = 'token')
    {
        $token = $this->getBearerToken();
        if (empty($token)) {
            $token = $this->request->header($name, '');
        }
        if (empty($token)) {
            $token = $this->request->input($name, '');
        }

        return $token;
    }

    /**
     * 创建token
     * @param int $id
     * @return string
     */
    public function create(int $id)
    {
        return $this->core->encrypt($id . ':' . time());
    }

    /**
     * 解析token
     * [加密数据, 加密时间]
     * @param string $token
     * @return array
     */
    public function parse(string $token)
    {
        return explode(':', $this->core->decrypt($token));
    }

    /**
     * 验证token
     * @param array $parse
     * @return array|bool
     */
    public function verify(array $parse)
    {
        // 验证格式
        if (count($parse) != 2) {
            return [ResultCode::TOKEN_INVALID, ResultMsg::TOKEN_INVALID];
        }

        // 验证token是否有效
        $refreshTime = $this->getAllowRefreshTime();
        if (($parse[1] + $refreshTime) < time()) {
            return [ResultCode::TOKEN_EXPIRE, ResultMsg::TOKEN_EXPIRE];
        }

        return true;
    }

    /**
     * 提示刷新时间
     * @return mixed
     */
    public function getNoticeRefreshTime()
    {
        return $this->core->getConfig('notice_refresh_time', 0);
    }

    /**
     * 允许刷新时间
     * @return mixed
     */
    public function getAllowRefreshTime()
    {
        return $this->core->getConfig('allow_refresh_time', 0);
    }

    /**
     * 刷新token
     * @return mixed
     */
    public function getRefreshName()
    {
        return $this->core->getConfig('refresh_name', 'token');
    }

    /**
     * 获取 BearerToken
     * @return string
     */
    protected function getBearerToken()
    {
        $header = $this->request->header('Authorization', '');

        if (Str::startsWith($header, 'Bearer ')) {
            return Str::substr($header, 7);
        }

        return '';
    }
}
