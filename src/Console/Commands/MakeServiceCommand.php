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
     * 获取生成内容
     * @param string $name
     * @return string
     */
    protected function getBuildContent(string $name)
    {
        $str = <<< EOF
<?php
namespace %s;

use Jmhc\Restful\Services\BaseService;

class %s extends BaseService
{}
EOF;
        return sprintf($str, $this->namespace, $name);
    }

    /**
     * 获取选项
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();

        return array_merge($options, [
            ['controller', null, InputOption::VALUE_NONE, 'Generate the controller file with the same name'],
            ['model', null, InputOption::VALUE_NONE, 'Generate the model file with the same name'],
        ]);
    }
}
