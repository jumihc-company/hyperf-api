<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Exceptions;

use Exception;

/**
 * 请求结果异常
 * @package Jmhc\Restful\Exceptions
 */
class ResultException extends Exception
{
    protected $data;
    protected $httpCode;

    public function __construct(int $code, string $msg, $data, int $httpCode)
    {
        $this->setData($data);
        $this->setHttpCode($httpCode);

        parent::__construct($msg, $code, null);
    }

    public function getData()
    {
        return $this->data;
    }

    public function getHttpCode()
    {
        return $this->httpCode;
    }

    protected function setData($data)
    {
        $this->data = $data;
    }

    protected function setHttpCode($httpCode)
    {
        $this->httpCode = $httpCode;
    }
}
