<?php
/**
 * User: YL
 * Date: 2019/11/25
 */

namespace Jmhc\Restful\Console\Commands\Traits;

/**
 * 替换辅助
 * @package Jmhc\Restful\Console\Commands\Traits
 */
trait ReplaceTrait
{
    /**
     * 替换命名空间
     * @param string $content
     * @param string $replace
     * @return $this
     */
    protected function replaceNameSpace(string &$content, string $replace)
    {
        $content = str_replace('%NAMESPACE%', $replace, $content);

        return $this;
    }

    /**
     * 替换导入
     * @param string $content
     * @param string $replace
     * @return $this
     */
    protected function replaceUses(string &$content, string $replace)
    {
        $content = str_replace('%USES%', $replace, $content);

        return $this;
    }

    /**
     * 替换 trait 导入
     * @param string $content
     * @param string $replace
     * @return $this
     */
    protected function replaceTraitUses(string &$content, string $replace)
    {
        $content = str_replace('%TRAIT_USES%', $replace, $content);

        return $this;
    }

    /**
     * 替换注释
     * @param string $content
     * @param string $replace
     * @return $this
     */
    protected function replaceAnnotations(string &$content, string $replace)
    {
        $content = str_replace('%ANNOTATIONS%', $replace, $content);

        return $this;
    }

    /**
     * 替换类名
     * @param string $content
     * @param string $replace
     * @return $this
     */
    protected function replaceClass(string &$content, string $replace)
    {
        $content = str_replace('%CLASS%', $replace, $content);

        return $this;
    }

    /**
     * 替换继承
     * @param string $content
     * @param string $replace
     * @return $this
     */
    protected function replaceExtends(string &$content, string $replace)
    {
        $content = str_replace('%EXTENDS%', $replace, $content);

        return $this;
    }

    /**
     * 替换实现
     * @param string $content
     * @param string $replace
     * @return $this
     */
    protected function replaceImplements(string &$content, string $replace)
    {
        $content = str_replace('%IMPLEMENTS%', $replace, $content);

        return $this;
    }
}
