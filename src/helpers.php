<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

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
