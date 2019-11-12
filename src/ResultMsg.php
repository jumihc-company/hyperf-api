<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful;

/**
 * 请求结果消息
 * @package Jmhc\Restful
 */
class ResultMsg
{
    const SUCCESS                 = 'success';
    const NO_DATA                 = '暂无数据';
    const MAINTENANCE             = '系统维护中...';
    const ERROR                   = 'error';
    const INVALID_REQUEST         = '无效的请求';
    const SDL                     = '当前账号已在其他设备登录';
    const TOKEN_NO_EXISTS         = 'token不存在';
    const TOKEN_INVALID           = 'token无效';
    const TOKEN_EXPIRE            = 'token已过期';
    const PROHIBIT_LOGIN          = '当前用户禁止登录';
    const SYS_EXCEPTION           = '系统异常，请稍后重试～';
    const SYS_ERROR               = '系统错误，请稍后重试～';
    const OLD_VERSION             = '当前应用版本过低';
}
