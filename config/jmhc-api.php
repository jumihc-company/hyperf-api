<?php

return [
    // 异常文件名称
    'db_exception_file_name' => env('JMHC_DB_EXCEPTION_FILE_NAME', 'handle_db.exception'),
    'sms_exception_file_name' => env('JMHC_SMS_EXCEPTION_FILE_NAME', 'sms.exception'),
    'exception_file_name' => env('JMHC_EXCEPTION_FILE_NAME', 'handle.exception'),
    'error_file_name' => env('JMHC_ERROR_FILE_NAME', 'handle.error'),

    // 单设备登录临时缓存过期时间（秒）
    'sdl_tmp_expire' => env('JMHC_SDL_TMP_EXPIRE', 10),

    // 跨域配置
    'cors' => [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Allow-Methods' => 'GET, POST, PATCH, PUT, DELETE',
        'Access-Control-Allow-Headers' => 'Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With',
        'Access-Control-Max-Age' =>  86400, // 1d
    ],

    // 日志配置
    'log' => [
        // 是否允许保存debug日志
        'debug' => env('JMHC_LOG_DEBUG', true),
        // 日志保存路径
        'path' => env('JMHC_LOG_PATH', 'runtime/logs'),
        // 日志文件最大内存,0不限制,如（2m,2g）
        'max_size' => env('JMHC_LOG_MAX_SIZE', 0),
        // 目录下最大日志文件数量,0不限制
        'max_files' => env('JMHC_LOG_MAX_FILES', 0),
    ],

    // 运行加密配置
    'runtime' => [
        // 运行调试模式
        'debug' => env('JMHC_RUNTIME_DEBUG', true),
        // 运行加密方法
        'method' => env('JMHC_RUNTIME_METHOD', 'AES-128-CBC'),
        // 运行加密向量
        'iv' => env('JMHC_RUNTIME_IV', ''),
        // 运行加密秘钥
        'key' => env('JMHC_RUNTIME_KEY', ''),
    ],

    // 令牌加密配置
    'token' => [
        // 令牌加密方法
        'method' => env('JMHC_TOKEN_METHOD', 'AES256'),
        // 令牌加密向量
        'iv' => env('JMHC_TOKEN_IV', ''),
        // 令牌加密秘钥
        'key' => env('JMHC_TOKEN_KEY', ''),
        // 令牌填充位置
        'pos' => env('JMHC_TOKEN_POS', 5),
        // 令牌填充长度
        'len' => env('JMHC_TOKEN_LEN', 6),
        // 令牌允许刷新时间（秒） 3天
        'allow_refresh_time' => env('JMHC_TOKEN_ALLOW_REFRESH_TIME', 259200),
        // 令牌提示刷新时间（秒） 2天
        'notice_refresh_time' => env('JMHC_TOKEN_NOTICE_REFRESH_TIME', 172800),
    ],

    // 签名配置
    'signature' => [
        // 是否检测签名
        'check' => env('JMHC_SIGNATURE_CHECK', false),
        // 签名秘钥
        'key' => env('JMHC_SIGNATURE_KEY', ''),
        // 签名时间戳超时（秒）
        'timestamp_timeout' => env('JMHC_SIGNATURE_TIMESTAMP_TIMEOUT', 60),
    ],

    // 请求锁定配置
    'request_lock' => [
        // 请求锁定时间（秒）
        'seconds' => env('JMHC_REQUEST_LOCK_SECONDS', 5),
        // 请求锁定提示
        'tips' => env('JMHC_REQUEST_LOCK_TIPS', '请求已被锁定，请稍后重试~'),
    ],
];
