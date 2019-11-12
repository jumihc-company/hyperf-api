<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Exceptions;

use Jmhc\Restful\ResultCode;
use Jmhc\Restful\ResultMsg;

/**
 * 无效请求异常
 * @package Jmhc\Restful\Exceptions
 */
class InvalidRequestException extends ResultException
{
    public function __construct(string $msg = ResultMsg::INVALID_REQUEST, int $code = ResultCode::ERROR, int $httpCode = ResultCode::HTTP_ERROR_CODE)
    {
        parent::__construct($code, $msg, null, $httpCode);
    }
}
