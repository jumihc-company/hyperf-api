<?php
/**
 * User: YL
 * Date: 2019/11/18
 */

namespace Jmhc\Restful\Console\Commands;

use Hyperf\Utils\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * 生成工厂
 * @package Jmhc\Restful\Console\Commands
 */
class MakeFactoryCommand extends AbstractMakeCommand
{
    /**
     * 命令名称
     * @var string
     */
    protected $name = 'jmhc-api:make-factory';

    /**
     * 命令描述
     * @var string
     */
    protected $description = 'Generate the factory file';

    /**
     * 默认保存路径
     * @var string
     */
    protected $defaultDir = 'Common/Factory/';

    /**
     * 实体名称
     * @var string
     */
    protected $entityName = 'Factory';

    /**
     * 参数 name
     * @var string
     */
    protected $argumentName;

    /**
     * 选项 scan-dir
     * @var array
     */
    protected $optionScanDir;

    /**
     * 选项 dir
     * @var string
     */
    protected $optionDir;

    /**
     * 选项 force
     * @var bool
     */
    protected $optionForce;

    /**
     * 选项 suffix
     * @var bool
     */
    protected $optionSuffix;

    /**
     * 主要操作
     */
    protected function mainHandle()
    {
        // 生成名称
        $name = $this->getBuildName($this->argumentName);

        // 保存文件
        $filePath = $this->dir . $name . '.php';

        // 存在且不覆盖
        if (file_exists($filePath) && ! $this->optionForce) {
            return false;
        }

        // 扫描的文件
        $scans = [];
        foreach ($this->optionScanDir as $dir) {
            foreach (glob($dir . '*.php') as $file) {
                if (strpos($file, BASE_PATH) !== 0) {
                    continue;
                }

                $_class = rtrim(basename($file), '\.php');
                $_file = str_replace([BASE_PATH, sprintf('/%s.php', $_class)], '', $file);
                $scans[] = [
                    'namespace' => sprintf(
                        '\\App%s\\%s',
                        str_replace('/', '\\', $_file),
                        $_class
                    ),
                    'method' => lcfirst($_class),
                ];
            }
        }

        // 生成文件
        $annotation = $this->getAnnotation($scans);
        $content = $this->getBuildContent($name, $annotation);
        file_put_contents($filePath, $content);
        $this->info($filePath . ' create Succeed!');

        return true;
    }

    /**
     * 获取生成名称
     * @param string $name
     * @return string
     */
    protected function getBuildName(string $name)
    {
        $name = Str::singular($this->filterStr($name));
        // 判断是否添加后缀
        if (! preg_match(sprintf('/%s$/i', $this->entityName), $name) && $this->optionSuffix) {
            $name .= '_' . $this->entityName;
        }
        return Str::studly($name);
    }

    /**
     * 获取生成内容
     * @param string $name
     * @param string $annotation
     * @return string
     */
    protected function getBuildContent(string $name, string $annotation)
    {
        $str = <<< EOF
<?php
namespace %s;

use Jmhc\Restful\Factory\BaseFactory;
%s
class %s extends BaseFactory
{}
EOF;
        return sprintf($str, $this->namespace, $annotation, $name);
    }

    /**
     * 获取注解
     * @param array $scans
     * @return string
     */
    protected function getAnnotation(array $scans)
    {
        if (empty($scans)) {
            return '';
        }

        $str = '/**';
        foreach ($scans as $scan) {
            $str .= PHP_EOL . sprintf(' * @method static %s %s(bool $refresh = false, array $params = [])', $scan['namespace'], $scan['method']);
        }

        return PHP_EOL . $str . PHP_EOL . ' * @package ' . $this->namespace . PHP_EOL . ' */';
    }

    /**
     * 获取保存文件夹
     * @return string
     */
    protected function getSaveDir()
    {
        if (! $this->optionDir) {
            return $this->defaultDir;
        }

        return $this->filterOptionDir($this->optionDir);
    }

    /**
     * 设置参数、选项
     */
    protected function setArgumentOption()
    {
        $this->argumentName = $this->filterArgumentName(
            $this->argument('name'),
            $this->entityName
        );
        $this->optionScanDir = array_map(function ($v) {
            return BASE_PATH . '/' . $this->filterOptionDir($v);
        }, $this->option('scan-dir'));
        $this->optionDir = $this->filterOptionDir($this->option('dir'));
        $this->optionForce = $this->option('force');
        $this->optionSuffix = $this->option('suffix');
    }

    /**
     * 获取参数
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, $this->entityName . ' name'],
        ];
    }

    /**
     * 获取选项
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['scan-dir', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'File scanning path, relative to app directory'],
            ['dir', null, InputOption::VALUE_REQUIRED, 'File saving path, relative to app directory', $this->defaultDir],
            ['force', 'f', InputOption::VALUE_NONE, 'Overwrite existing files'],
            ['suffix', 's', InputOption::VALUE_NONE, sprintf('Add the `%s` suffix', $this->entityName)],
        ];
    }
}
