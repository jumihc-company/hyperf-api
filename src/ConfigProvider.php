<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful;

use Jmhc\Restful\Contracts\UserInterface;
use Jmhc\Restful\Contracts\VersionInterface;
use Jmhc\Restful\Middleware\CoreMiddleware;
use Jmhc\Restful\Models\UserModel;
use Jmhc\Restful\Models\VersionModel;
use Jmhc\Restful\Utils\Log;

/**
 * 配置服务
 * @package Jmhc\Restful
 */
class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                \Hyperf\HttpServer\CoreMiddleware::class => CoreMiddleware::class,
                Log::class => Log::class,
                UserInterface::class => UserModel::class,
                VersionInterface::class => VersionModel::class,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'description of this config file.',
                    'source' => jmhc_api_config_path('jmhc-api.php'),
                    'destination' => BASE_PATH . '/config/autoload/jmhc-api.php',
                ],
            ],
            'jmhc-api' => file_get_contents(jmhc_api_config_path('jmhc-api.php')),
        ];
    }
}
