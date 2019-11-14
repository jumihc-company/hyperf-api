<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Models;

use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Jmhc\Restful\Contracts\UserModelInterface;

/**
 * 用户模型
 * @package Jmhc\Restful\Models
 */
class UserModel extends BaseModel implements UserModelInterface
{
    /**
     * 通过id获取信息
     * @param int $id
     * @return Builder|Model|object|null
     */
    public function getInfoById(int $id)
    {
        return static::query()
            ->where('id', $id)
            ->first();
    }
}
