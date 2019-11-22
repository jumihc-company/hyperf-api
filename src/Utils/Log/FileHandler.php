<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful\Utils\Log;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\Str;
use Jmhc\Restful\Utils\FileSize;

/**
 * 文件日志处理器
 * @package Jmhc\Restful\Utils\Log
 */
class FileHandler
{
    /**
     * @var ConfigInterface
     */
    protected $configInterface;

    /**
     * @var string
     */
    protected $path = 'runtime/logs';

    /**
     * @var string
     */
    protected $maxSize = 0;

    /**
     * @var int
     */
    protected $maxFiles = 0;

    /**
     * @var string
     */
    protected $savePath;

    public function __construct(
        ConfigInterface $configInterface,
        array $config = []
    )
    {
        $this->configInterface = $configInterface;

        $this->initialize($config);
    }

    /**
     * 初始化操作
     * @param array $config
     */
    protected function initialize(array $config = [])
    {
        // 设置配置值
        $this->setConfig(array_merge(
            $this->configInterface->get('jmhc-api.log', []),
            $config
        ));

        $this->setSavePath();
        $this->checkDir();
        $this->checkMaxFiles();
    }

    /**
     * 是否调试模式
     * @return mixed
     */
    public function isDebug()
    {
        return $this->configInterface->get('jmhc-api.log.debug', true);
    }

    /**
     * 写入文件
     * @param string $name
     * @param string $msg
     * @return bool
     */
    public function write(string $name, string $msg)
    {
        $name = $this->buildName($name);
        $msg = $this->buildMsg($msg);

        $this->checkMaxSize($name);

        return !! file_put_contents($name, $msg, FILE_APPEND);
    }

    /**
     * 设置保存路径
     */
    protected function setSavePath()
    {
        $this->savePath = base_path(trim($this->path, '/') . '/');
    }

    /**
     * 设置配置
     * @param array $config
     */
    protected function setConfig(array $config)
    {
        foreach ($config as $k => $v) {
            $_name = Str::camel($k);
            if (property_exists($this, $_name) && isset($v)) {
                $this->{$_name} = $v;
            }
        }
    }

    /**
     * 生成路径
     * @param string $name
     * @return string
     */
    protected function buildName(string $name)
    {
        return sprintf(
            '%s%s%s.log',
            $this->savePath,
            date('Y-m-d'),
            ! empty($name) ? '.' . $name : ''
        );
    }

    /**
     * 生成消息
     * @param string $msg
     * @return string
     */
    protected function buildMsg(string $msg)
    {
        return sprintf(
            '[%s]%s%s%s',
            date('Y-m-d H:i:s'),
            PHP_EOL,
            $msg,
            PHP_EOL
        );
    }

    /**
     * 检测路径
     */
    protected function checkDir()
    {
        if (! is_dir($this->savePath)) {
            mkdir($this->savePath, 0755, true);
        }
    }

    /**
     * 检测最大文件数量
     */
    protected function checkMaxFiles()
    {
        if ($this->maxFiles) {
            $files = glob($this->savePath . '*.log');
            if (count($files) > $this->maxFiles) {
                unlink($files[0]);
            }
        }
    }

    /**
     * 检测最大文件占用量
     * @param string $name
     */
    protected function checkMaxSize(string $name)
    {
        if ($this->maxSize) {
            $fileSize = FileSize::get($this->maxSize);
            if (is_file($name) && floor($fileSize) <= filesize($name)) {
                rename($name, dirname($name) . DIRECTORY_SEPARATOR . time() . '-' . basename($name));
            }
        }
    }
}
