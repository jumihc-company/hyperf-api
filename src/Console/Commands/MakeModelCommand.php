<?php
/**
 * User: YL
 * Date: 2019/10/22
 */

namespace Jmhc\Restful\Console\Commands;

use Symfony\Component\Console\Input\InputOption;

/**
 * 生成模型
 * @package Jmhc\Restful\Console\Commands
 */
class MakeModelCommand extends MakeCommand
{
    /**
     * 命令名称
     * @var string
     */
    protected $name = 'jmhc-api:make-model';

    /**
     * 实体名称
     * @var string
     */
    protected $entityName = 'Model';

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

use Jmhc\Restful\Models\BaseModel;

class %s extends BaseModel
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
            ['service', null, InputOption::VALUE_NONE, 'Generate the service file with the same name'],
        ]);
    }
}
