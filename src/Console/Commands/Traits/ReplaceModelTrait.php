<?php
/**
 * User: YL
 * Date: 2019/11/25
 */

namespace Jmhc\Restful\Console\Commands\Traits;

/**
 * 替换模型辅助
 * @package Jmhc\Restful\Console\Commands\Traits
 */
trait ReplaceModelTrait
{
    /**
     * 替换表名
     * @param string $content
     * @param string $replace
     * @return $this
     */
    protected function replaceTable(string &$content, string $replace)
    {
        $content = str_replace('%TABLE%', $replace, $content);

        return $this;
    }

    /**
     * 替换批量赋值属性
     * @param string $content
     * @param string $replace
     * @return $this
     */
    protected function replaceFillable(string &$content, string $replace)
    {
        $content = str_replace('%FILLABLE%', $replace, $content);

        return $this;
    }

    /**
     * 替换时间字段属性
     * @param string $content
     * @param string $replace
     * @return $this
     */
    protected function replaceDates(string &$content, string $replace)
    {
        $content = str_replace('%DATES%', $replace, $content);

        return $this;
    }

    /**
     * 替换属性类型转换
     * @param string $content
     * @param string $replace
     * @return $this
     */
    protected function replaceCasts(string &$content, string $replace)
    {
        $content = str_replace('%CASTS%', $replace, $content);

        return $this;
    }
}
