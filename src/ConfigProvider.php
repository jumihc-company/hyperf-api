<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful;

use Jmhc\Restful\Contracts\UserModelInterface;
use Jmhc\Restful\Contracts\VersionModelInterface;
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
                UserModelInterface::class => UserModel::class,
                VersionModelInterface::class => VersionModel::class,
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
                    'id' => 'config-jmhc-api',
                    'description' => 'API profile.',
                    'source' => jmhc_api_config_path('jmhc-api.php'),
                    'destination' => BASE_PATH . '/config/autoload/jmhc-api.php',
                ],
                [
                    'id' => 'database-users',
                    'description' => 'User table migration file.',
                    'source' => jmhc_api_database_path('migrations/2019_11_21_151645_create_users_table.php'),
                    'destination' => BASE_PATH . '/migrations/2019_11_21_151645_create_users_table.php',
                ],
                [
                    'id' => 'database-version',
                    'description' => 'Version table migration file.',
                    'source' => jmhc_api_database_path('migrations/2019_11_21_151651_create_versions_table.php'),
                    'destination' => BASE_PATH . '/migrations/2019_11_21_151651_create_versions_table.php',
                ],
            ],
            'jmhc-api' => file_get_contents(jmhc_api_config_path('jmhc-api.php')),
        ];
    }
}
