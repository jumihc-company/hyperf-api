<?php
/**
 * User: YL
 * Date: 2019/11/12
 */

namespace Jmhc\Restful;

use Jmhc\Restful\Console\Commands\DownCommand;
use Jmhc\Restful\Console\Commands\MakeControllerCommand;
use Jmhc\Restful\Console\Commands\MakeFactoryCommand;
use Jmhc\Restful\Console\Commands\MakeModelCommand;
use Jmhc\Restful\Console\Commands\MakeServiceCommand;
use Jmhc\Restful\Console\Commands\MakeWithFileCommand;
use Jmhc\Restful\Console\Commands\UpCommand;
use Jmhc\Restful\Contracts\UserModelInterface;
use Jmhc\Restful\Contracts\VersionModelInterface;
use Jmhc\Restful\Middleware\CoreMiddleware;
use Jmhc\Restful\Models\UserModel;
use Jmhc\Restful\Models\VersionModel;
use Jmhc\Restful\Scopes\PrimaryKeyDescScope;
use Jmhc\Restful\Utils\Container;
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
                Container::class => Container::class,
                Log::class => Log::class,
                PrimaryKeyDescScope::class => PrimaryKeyDescScope::class,
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
            'commands' => [
                MakeControllerCommand::class,
                MakeModelCommand::class,
                MakeServiceCommand::class,
                MakeFactoryCommand::class,
                MakeWithFileCommand::class,
                DownCommand::class,
                UpCommand::class,
            ],
            'publish' => [
                [
                    'id' => 'config-jmhc-api',
                    'description' => 'API profile.',
                    'source' => jmhc_api_config_path('jmhc-api.php'),
                    'destination' => config_autoload_path('jmhc-api.php'),
                ],
                [
                    'id' => 'config-jmhc-build-file',
                    'description' => 'Build file.',
                    'source' => jmhc_api_config_path('jmhc-build-file.php'),
                    'destination' => config_autoload_path( 'jmhc-build-file.php'),
                ],
                [
                    'id' => 'config-jmhc-sms',
                    'description' => 'SMS profile.',
                    'source' => jmhc_api_config_path('jmhc-sms.php'),
                    'destination' => config_autoload_path('jmhc-sms.php'),
                ],
                [
                    'id' => 'database-users',
                    'description' => 'User table migration file.',
                    'source' => jmhc_api_database_path('migrations/2019_11_21_151645_create_users_table.php'),
                    'destination' => migration_path('2019_11_21_151645_create_users_table.php'),
                ],
                [
                    'id' => 'database-version',
                    'description' => 'Version table migration file.',
                    'source' => jmhc_api_database_path('migrations/2019_11_21_151651_create_versions_table.php'),
                    'destination' => migration_path('2019_11_21_151651_create_versions_table.php'),
                ],
            ],
        ];
    }
}
