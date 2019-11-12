<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Utils;

use Jmhc\Restful\Utils\Cipher\Runtime;

/**
 * 运行加、解密
 * @package Jmhc\Restful\Utils
 */
class Cipher
{
    /**
     * @var Runtime
     */
    protected $core;

    public function __construct(
        Runtime $core
    )
    {
        $this->core = $core;
    }

    /**
     * 请求
     * @param string $params
     * @return string
     */
    public function request(string $params)
    {
        if ($this->isExec()) {
            return $this->core->decrypt($params);
        }

        return $params;
    }

    /**
     * 响应
     * @param array $data
     * @return array|string
     */
    public function response(array $data)
    {
        if ($this->isExec()) {
            return $this->core->decrypt(json_encode($data));
        }

        return $data;
    }

    /**
     * 是否运行
     * @return bool
     */
    protected function isExec()
    {
        return ! $this->core->getConfig('debug', true);
    }
}
