<?php
/**
 * User: YL
 * Date: 2019/10/22
 */

namespace Jmhc\Restful\Console\Commands;

use Hyperf\Utils\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

abstract class MakeCommand extends AbstractMakeCommand
{
    /**
     * 命令描述
     * @var string
     */
    protected $description = 'Generate the %s file';

    /**
     * 实体名称
     * @var string
     */
    protected $entityName;

    /**
     * 默认保存路径
     * @var string
     */
    protected $defaultDir = 'Http/';

    /**
     * 参数 name
     * @var string
     */
    protected $argumentName;

    /**
     * 选项 dir
     * @var string
     */
    protected $optionDir;

    /**
     * 选项 module
     * @var string
     */
    protected $optionModule;

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

    abstract protected function getBuildContent(string $name);

    public function __construct(string $name = null)
    {
        $this->description = sprintf(
            $this->description,
            strtolower($this->entityName)
        );

        parent::__construct($name);
    }

    /**
     * 主要操作
     */
    protected function mainHandle()
    {
        // 生成名称
        $name = $this->getBuildName($this->argumentName);

        // 保存文件
        $filePath = $this->dir . $name . '.php';
        if (! file_exists($filePath) || $this->optionForce) {
            $content = $this->getBuildContent($name);
            file_put_contents($filePath, $content);
            $this->info($filePath . ' create Succeed!');
        }

        // 执行额外命令
        $this->extraCommands();
    }

    /**
     * 获取保存文件夹
     * @return string
     */
    protected function getSaveDir()
    {
        // 路径
        $dir = $this->defaultDir;
        if ($this->optionDir) {
            $dir = $this->filterOptionDir($this->optionDir);
            // 路径不存在实体后缀
            if (! preg_match(sprintf('/[(%ss\/)(%s)]$/i', $this->entityName, $this->entityName), $dir)) {
                $dir .= $this->entityName . 's/';
            }
        }

        // 模块存在
        if ($this->optionModule) {
            $dir = preg_replace(
                    sprintf('/%ss\/$/i', $this->entityName),
                    '',
                    $this->optionDir
                ) . $this->filterStr($this->optionModule) . '/' . $this->entityName . 's/';
        }

        return $dir;
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
     * 执行额外命令
     */
    protected function extraCommands()
    {
        // 名称
        $name = $this->filterArgumentName($this->argumentName, $this->entityName);

        // 命令参数
        $arguments = [
            'name' => $name,
            '--module' => $this->optionModule,
            '--force' => $this->optionForce,
            '--suffix' => $this->optionSuffix,
        ];
        // 保存路径
        $saveDir = $this->getSaveDir();
        if ($this->optionModule) {
            $saveDir = str_replace($this->optionModule . '/', '', $this->getSaveDir());
        }
        // 路径格式
        $dirFormat = str_replace(
            $this->entityName,
            '%',
            $saveDir
        );

        // 创建控制器
        if ($this->hasOption('controller') && $this->option('controller')) {
            $_dir = sprintf($dirFormat, 'Controllers');
            $arguments['--dir'] = $_dir;
            $this->call('jmhc-api:make-controller', $arguments);
        }

        // 创建模型
        if ($this->hasOption('model') && $this->option('model')) {
            $_dir = sprintf($dirFormat, 'Models');
            $arguments['--dir'] = $_dir;
            $this->call('jmhc-api:make-model', $arguments);
        }

        // 创建服务
        if ($this->hasOption('service') && $this->option('service')) {
            $_dir = sprintf($dirFormat, 'Services');
            $arguments['--dir'] = $_dir;
            $this->call('jmhc-api:make-service', $arguments);
        }

        // 创建迁移
        if ($this->option('migration')) {
            $this->call('gen:migration', [
                'name' => sprintf(
                    'create_%s_table',
                    Str::plural(Str::snake($name))
                )
            ]);
        }
    }

    /**
     * 设置参数、选项
     */
    protected function setArgumentOption()
    {
        // 命令参数
        $this->argumentName = $this->argument('name');

        // 命令选项
        $this->optionDir = $this->filterOptionDir($this->option('dir'));
        $this->optionModule = ucfirst($this->option('module'));
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
            ['dir', null, InputOption::VALUE_REQUIRED, 'File saving path, relative to app directory', $this->defaultDir . $this->entityName . 's/'],
            ['module', 'm', InputOption::VALUE_REQUIRED, 'Module name'],
            ['force', 'f', InputOption::VALUE_NONE, 'Overwrite existing file'],
            ['suffix', 's', InputOption::VALUE_NONE, sprintf('Add the `%s` suffix', $this->entityName)],
            ['migration', null, InputOption::VALUE_NONE, 'Generate the migration file with the same name'],
        ];
    }
}
