<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Services;

use Jmhc\Restful\Contracts\ConstAttribute;
use Jmhc\Restful\Traits\RequestInfoTrait;
use Jmhc\Restful\Traits\ResourceServiceTrait;
use Jmhc\Restful\Traits\ResultThrowTrait;
use Jmhc\Restful\Traits\UserInfoTrait;

/**
 * 基础服务
 * @package Jmhc\Restful\Services
 */
class BaseService implements ConstAttribute
{
    use ResourceServiceTrait;
    use RequestInfoTrait;
    use UserInfoTrait;
    use ResultThrowTrait;

    /**
     * 更新属性
     * @return $this
     */
    public function updateAttribute()
    {
        // 设置请求信息
        $this->setRequestInfo();

        return $this;
    }
}
