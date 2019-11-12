<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Contracts;

/**
 * 用户模型
 * @package Jmhc\Restful\Contracts
 */
interface UserInterface
{
    // 通过id获取信息
    public static function getInfoById(int $id);
}
