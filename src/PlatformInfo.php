<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful;

/**
 * 平台信息
 * @package Jmhc\Restful
 */
class PlatformInfo
{
    const OTHER = 'other';
    const ANDROID = 'android';
    const IOS = 'ios';
    const WEI_MP = 'wei_mp';
    const ALI_MP = 'ali_mp';

    /**
     * @var array
     */
    protected static $platforms = [
        'JmhcAndroid' => self::ANDROID,
        'JmhcIos' => self::IOS,
        'JmhcWeiMp' => self::WEI_MP,
        'JmhcAliMp' => self::ALI_MP,
    ];

    /**
     * 获取所有平台
     * @return array
     */
    public static function getAllPlatform()
    {
        return static::$platforms;
    }
}
