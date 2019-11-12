<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Traits;

use Jmhc\Restful\Exceptions\ResultException;
use Jmhc\Restful\ResultCode;
use Jmhc\Restful\ResultMsg;

/**
 * 抛出结果异常
 * @package Jmhc\Restful\Traits
 */
trait ResultThrowTrait
{
    /**
     * 抛出成功异常
     * @param $data
     * @param string $msg
     * @param int $code
     * @throws ResultException
     */
    protected function success($data = null, string $msg = ResultMsg::SUCCESS, int $code = ResultCode::SUCCESS)
    {
        $this->abort($code, $msg, $data);
    }

    /**
     * 抛出失败异常
     * @param string $msg
     * @param int $code
     * @param array $data
     * @param int $httpCode
     * @throws ResultException
     */
    protected function error(string $msg, int $code = ResultCode::ERROR, $data = null, int $httpCode = ResultCode::HTTP_ERROR_CODE)
    {
        $this->abort($code, $msg, $data, $httpCode);
    }

    /**
     * 抛出无数据异常
     * @param string $msg
     * @throws ResultException
     */
    protected function noData(string $msg = ResultMsg::NO_DATA)
    {
        $this->abort(ResultCode::NO_DATA, $msg, null);
    }

    /**
     * 抛出异常
     * @param int $code
     * @param string $msg
     * @param $data
     * @param int $httpCode
     * @throws ResultException
     */
    private function abort(int $code, string $msg, $data, int $httpCode = ResultCode::HTTP_SUCCESS_CODE)
    {
        throw new ResultException($code, $msg, $data, $httpCode);
    }
}
