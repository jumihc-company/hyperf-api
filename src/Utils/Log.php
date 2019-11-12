<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Utils;

use Jmhc\Restful\Utils\Log\FileHandler;

/**
 * 日志
 * @package Jmhc\Restful\Utils
 */
class Log
{
    /**
     * 配置
     * @var array
     */
    protected $config = [];

    /**
     * 设置配置
     * @param array $config
     * @return static
     */
    public function setConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * 调试日志
     * @param string $name
     * @param string $msg
     * @param mixed ...$params
     * @return bool
     */
    public function debug(string $name, string $msg, ...$params)
    {
        if ($this->getFileHandler()->isDebug()) {
            return $this->save($name, $msg, ...$params);
        }

        // 重置配置
        $this->config = [];

        return true;
    }

    /**
     * 保存
     * @param string $name
     * @param string $msg
     * @param mixed ...$params
     * @return mixed
     */
    public function save(string $name, string $msg, ...$params)
    {
        if (! empty($params)) {
            $msg = sprintf($msg, ...$params);
        }

        $result = $this->getFileHandler()->write($name, $msg);

        // 重置配置
        $this->config = [];

        return $result;
    }

    /**
     * 获取文件操作
     * @return FileHandler
     */
    protected function getFileHandler()
    {
        if (! empty($this->config)) {
            return make(FileHandler::class, [
                'config' => $this->config,
            ]);
        }

        return make(FileHandler::class);
    }
}
