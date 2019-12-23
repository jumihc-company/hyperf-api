<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Controllers;

use Jmhc\Restful\Traits\RequestInfoTrait;
use Jmhc\Restful\Traits\UserInfoTrait;

/**
 * 基础控制器
 * @package Jmhc\Restful\Controllers
 */
class BaseController
{
    use RequestInfoTrait;
    use UserInfoTrait;
}
