<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Exceptions;

use Jmhc\Restful\ResultCode;
use Jmhc\Restful\ResultMsg;

/**
 * 维护模式异常
 * @package Jmhc\Restful\Exceptions
 */
class MaintenanceModeException extends ResultException
{
    public function __construct(string $msg = ResultMsg::MAINTENANCE, int $code = ResultCode::MAINTENANCE, int $httpCode = ResultCode::HTTP_ERROR_CODE)
    {
        parent::__construct($code, $msg, null, $httpCode);
    }
}
