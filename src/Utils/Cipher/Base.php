<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Utils\Cipher;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;

/**
 * 加密基类
 * @package Jmhc\Restful\Utils\Cipher
 */
abstract class Base
{
    /**
     * @Inject()
     * @var ConfigInterface
     */
    protected $configInterface;

    /**
     * @var array
     */
    protected $config;

    /**
     * 加密方法
     * @var string
     */
    protected $method;

    /**
     * 加密向量
     * @var string
     */
    protected $iv;

    /**
     * 加密key
     * @var string
     */
    protected $key;

    abstract public function encrypt(string $str);

    abstract public function decrypt(string $str);

    public function __construct()
    {
        // 初始化
        $this->initialize();
    }

    /**
     * 初始化
     */
    protected function initialize()
    {
        // 场景
        $scene = strtolower(get_called_class());
        // 配置
        $this->config = $this->configInterface->get(
            sprintf('jmhc-api.%s', $scene),
            []
        );

        $this->method = $this->config['method'] ?? 0;
        $this->iv = $this->config['iv'] ?? 0;
        $this->key = $this->config['key'] ?? 0;
    }

    /**
     * 获取配置
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function getConfig(string $key = '', $default = null)
    {
        if (empty($key)) {
            return $this->config;
        }

        return $this->config[$key] ?? $default;
    }
}
