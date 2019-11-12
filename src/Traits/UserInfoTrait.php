<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Traits;

use Hyperf\Database\Model\Model;
use Jmhc\Restful\Utils\Collection;

/**
 * 用户信息
 * @package Jmhc\Restful\Traits
 */
trait UserInfoTrait
{
    /**
     * 登录用户id
     * @var int
     */
    protected $userId = 0;

    /**
     * 登录用户信息
     * @var Collection|Model
     */
    protected $userInfo;

    /**
     * 设置用户信息
     * @return $this
     */
    public function setUserInfo()
    {
        $this->userInfo = $this->request->userInfo ?? new Collection();
        $this->userId = $this->userInfo->id ?? 0;

        return $this;
    }
}
