<?php

use Overtrue\EasySms\Gateways\ChuanglanGateway;
use Overtrue\EasySms\Strategies\OrderStrategy;

return [
    // HTTP 请求的超时时间（秒）
    'timeout' => 5.0,

    // 默认发送配置
    'default' => [
        // 网关调用策略，默认：顺序调用
        'strategy' => OrderStrategy::class,

        // 默认可用的发送网关
        'gateways' => [
            'chuanglan',
        ],
    ],
    // 可用的网关配置
    'gateways' => [
        'chuanglan' => [
            'account' => env('CHUANGLAN_ACCOUNT'),
            'password' => env('CHUANGLAN_PASSWORD'),

            // 国际短信时必填
            'intel_account' => '',
            'intel_password' => '',

            // ChuanglanGateway::CHANNEL_VALIDATE_CODE  => 验证码通道（默认）
            // ChuanglanGateway::CHANNEL_PROMOTION_CODE => 会员营销通道
            'channel'  => ChuanglanGateway::CHANNEL_VALIDATE_CODE,

            // 会员营销通道 特定参数。创蓝规定：api提交营销短信的时候，需要自己加短信的签名及退订信息
            'sign' => '【通讯云】',
            'unsubscribe' => '回TD退订',
        ],
    ],
];
