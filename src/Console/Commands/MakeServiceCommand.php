<?php
/**
 * User: YL
 * Date: 2019/10/22
 */

namespace Jmhc\Restful\Console\Commands;

use Symfony\Component\Console\Input\InputOption;

/**
 * 生成服务
 * @package Jmhc\Restful\Console\Commands
 */
class MakeServiceCommand extends MakeCommand
{
    /**
     * 命令名称
     * @var string
     */
    protected $name = 'jmhc-api:make-service';

    /**
     * 实体名称
     * @var string
     */
    protected $entityName = 'Service';

    /**
     * 模板路径
     * @var string
     */
    protected $stubPath = __DIR__ . '/stubs/service.stub';

    /**
     * 命令配置
     */
    protected function configure()
    {
        parent::configure();

        $this->addOption('controller', null, InputOption::VALUE_NONE, 'Generate the controller file with the same name');
        $this->addOption('model', null, InputOption::VALUE_NONE, 'Generate the model file with the same name');
    }
}
