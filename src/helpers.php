<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

if (! function_exists('base_path')) {
    /**
     * 根路径
     * @param string $path
     * @return string
     */
    function base_path($path = '')
    {
        return BASE_PATH . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $path);
    }
}

if (! function_exists('app_path')) {
    /**
     * app路径
     * @param string $path
     * @return string
     */
    function app_path($path = '')
    {
        return BASE_PATH . DIRECTORY_SEPARATOR . 'app' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $path);
    }
}

if (! function_exists('config_path')) {
    /**
     * 配置路径
     * @param string $path
     * @return string
     */
    function config_path($path = '')
    {
        return BASE_PATH . DIRECTORY_SEPARATOR . 'config' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $path);
    }
}

if (! function_exists('config_autoload_path')) {
    /**
     * 配置自动加载路径
     * @param string $path
     * @return string
     */
    function config_autoload_path($path = '')
    {
        return BASE_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'autoload' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $path);
    }
}

if (! function_exists('migration_path')) {
    /**
     * 数据迁移路径
     * @param string $path
     * @return string
     */
    function migration_path($path = '')
    {
        return BASE_PATH . DIRECTORY_SEPARATOR . 'migrations' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $path);
    }
}

/**
 * 获取基础路径
 * @return string
 */
function jmhc_api_base_path()
{
    return dirname(__DIR__);
}

/**
 * 获取配置文件路径
 * @param string $path
 * @return string
 */
function jmhc_api_config_path(string $path = '')
{
    return jmhc_api_base_path() . DIRECTORY_SEPARATOR . 'config' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $path);
}

/**
 * 获取数据文件路径
 * @param string $path
 * @return string
 */
function jmhc_api_database_path(string $path = '')
{
    return jmhc_api_base_path() . DIRECTORY_SEPARATOR . 'database' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $path);
}
