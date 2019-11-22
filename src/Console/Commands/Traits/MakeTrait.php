<?php
/**
 * User: YL
 * Date: 2019/11/18
 */

namespace Jmhc\Restful\Console\Commands\Traits;

use Hyperf\Utils\Str;

/**
 * 创建命令辅助
 * @package Jmhc\Restful\Console\Commands\Traits
 */
trait MakeTrait
{

    /**
     * Determine if the given argument is present.
     *
     * @param  string|int  $name
     * @return bool
     */
    public function hasArgument($name)
    {
        return $this->input->hasArgument($name);
    }

    /**
     * Get the value of a command argument.
     *
     * @param  string|null  $key
     * @return string|array|null
     */
    public function argument($key = null)
    {
        if (is_null($key)) {
            return $this->input->getArguments();
        }

        return $this->input->getArgument($key);
    }

    /**
     * Determine if the given option is present.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasOption($name)
    {
        return $this->input->hasOption($name);
    }

    /**
     * Get the value of a command option.
     *
     * @param  string|null  $key
     * @return string|array|bool|null
     */
    public function option($key = null)
    {
        if (is_null($key)) {
            return $this->input->getOptions();
        }

        return $this->input->getOption($key);
    }

    /**
     * 过滤字符串
     * @param string $str
     * @return string
     */
    protected function filterStr(string $str)
    {
        return str_replace(['/', '\\'], '', $str);
    }

    /**
     * 过滤路径
     * @param string $dir
     * @return array
     */
    protected function filterDir(string $dir)
    {
        return array_filter(
            explode(
                '/',
                str_replace('\\', '', $dir)
            )
        );
    }

    /**
     * 获取路径字符串
     * @param array $dir
     * @return string
     */
    protected function getDirStr(array $dir)
    {
        $res = '';
        foreach ($dir as $v) {
            $res .= ucfirst($v) . '/';
        }
        return $res;
    }

    /**
     * 过滤选项路径
     * @param string $dir
     * @return string
     */
    protected function filterOptionDir(string $dir)
    {
        return $this->getDirStr($this->filterDir($dir));
    }


    /**
     * 创建文件夹
     * @param string $dir
     * @return bool
     */
    protected function createDir(string $dir)
    {
        return ! is_dir($dir) && mkdir($dir, 0755, true);
    }

    /**
     * 获取命名空间
     * @param string $dir
     * @return string
     */
    protected function getNamespace(string $dir)
    {
        return 'App\\' . str_replace('/', '\\', rtrim($dir, '/'));
    }

    /**
     * 过滤参数名称
     * @param string $name
     * @param string $suffix
     * @return string
     */
    protected function filterArgumentName(string $name, string $suffix)
    {
        return Str::singular(preg_replace(
            sprintf('/%s$/i', $suffix),
            '',
            $this->filterStr($name)
        ));
    }

    /**
     * 运行完成
     */
    protected function runComplete()
    {
        $this->info(sprintf('Command %s run completed!', $this->name));
    }
}
