<?php
/**
 * User: YL
 * Date: 2019/10/22
 */

namespace Jmhc\Restful\Console\Commands;

use Hyperf\Command\Command;
use Hyperf\Utils\Str;
use Jmhc\Restful\Console\Commands\Traits\CommandTrait;
use Jmhc\Restful\Console\Commands\Traits\MakeTrait;
use Jmhc\Restful\Console\Commands\Traits\ReplaceTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

abstract class MakeCommand extends Command
{
    use CommandTrait;
    use MakeTrait;
    use ReplaceTrait;

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
     * 参数 name 模式
     * @var int
     */
    protected $argumentNameMode = InputArgument::REQUIRED;

    /**
     * 文件保存路径
     * @var string
     */
    protected $dir;

    /**
     * 命名空间
     * @var string
     */
    protected $namespace;

    /**
     * 模板路径
     * @var string
     */
    protected $stubPath;

    /**
     * 生成类名称
     * @var string
     */
    protected $class;

    /**
     * 导入对象
     * @var string
     */
    protected $uses;

    /**
     * 继承对象
     * @var string
     */
    protected $extends;

    /**
     * 保存文件路径
     * @var string
     */
    protected $saveFilePath;

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

    /**
     * 选项 model-extends-pivot
     * @var bool
     */
    protected $optionModelExtendsPivot;

    /**
     * 选项 model-extends-mongo
     * @var bool
     */
    protected $optionModelExtendsMongo;

    /**
     * 选项 controller-extends-custom
     * @var string
     */
    protected $optionControllerExtendsCustom;

    /**
     * 选项 model-extends-custom
     * @var string
     */
    protected $optionModelExtendsCustom;

    /**
     * 选项 service-extends-custom
     * @var string
     */
    protected $optionServiceExtendsCustom;

    public function __construct()
    {
        $this->description = sprintf(
            $this->description,
            strtolower($this->entityName)
        );

        parent::__construct();
    }

    public function handle()
    {
        // 设置参数、选项
        $this->setArgumentOption();

        // 获取保存文件夹
        $dir = $this->getSaveDir();
        // 保存文件夹
        $this->dir = app_path($dir);
        // 命名空间
        $this->namespace = $this->getNamespace($dir);

        // 创建文件夹
        $this->createDir($this->dir);

        // 运行
        $this->mainHandle();

        // 运行完成
        $this->runComplete();
    }

    /**
     * 主要操作
     */
    protected function mainHandle()
    {
        // 生成类名称
        $this->class = $this->getClass($this->argumentName);

        // 保存文件
        $this->saveFilePath = $this->dir . $this->class . '.php';

        // 存在且不覆盖
        if (file_exists($this->saveFilePath) && ! $this->optionForce) {
            return false;
        }

        // 生成操作
        $this->buildHandle();

        // 执行额外命令
        $this->extraCommands();

        return true;
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
     * 生成操作
     */
    protected function buildHandle()
    {
        file_put_contents($this->saveFilePath, $this->getBuildContent());
        $this->info($this->saveFilePath . ' create Succeed!');
    }

    /**
     * 获取生成类名称
     * @param string $name
     * @return string
     */
    protected function getClass(string $name)
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
     * @return string
     */
    protected function getBuildContent()
    {
        $content = file_get_contents($this->stubPath);

        // 替换
        $this->replaceNamespace($content, $this->namespace)
            ->replaceClass($content, $this->class)
            ->replaceUses($content, $this->uses)
            ->replaceExtends($content, $this->extends);

        return $content;
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
            '--model-extends-pivot' => $this->optionModelExtendsPivot,
            '--model-extends-mongo' => $this->optionModelExtendsMongo,
            '--controller-extends-custom' => $this->optionControllerExtendsCustom,
            '--model-extends-custom' => $this->optionModelExtendsCustom,
            '--service-extends-custom' => $this->optionServiceExtendsCustom,
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
        $this->argumentName = $this->argument('name') ?? '';

        // 命令选项
        $this->optionDir = $this->filterOptionDir($this->option('dir'));
        $this->optionModule = ucfirst($this->option('module'));
        $this->optionForce = $this->option('force');
        $this->optionSuffix = $this->option('suffix');
        $this->optionModelExtendsPivot = $this->option('model-extends-pivot');
        $this->optionModelExtendsMongo = $this->option('model-extends-mongo');
        $this->optionControllerExtendsCustom = $this->getCommandClass($this->option('controller-extends-custom'));
        $this->optionModelExtendsCustom = $this->getCommandClass($this->option('model-extends-custom'));
        $this->optionServiceExtendsCustom = $this->getCommandClass($this->option('service-extends-custom'));
    }

    /**
     * 命令配置
     */
    protected function configure()
    {
        $this->addArgument('name', $this->argumentNameMode, $this->entityName . ' name');

        $this->addOption('dir', null, InputOption::VALUE_REQUIRED, 'File saving path, relative to app directory', $this->defaultDir . $this->entityName . 's/');
        $this->addOption('module', 'm', InputOption::VALUE_REQUIRED, 'Module name');
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing file');
        $this->addOption('suffix', 's', InputOption::VALUE_NONE, sprintf('Add the `%s` suffix', $this->entityName));
        $this->addOption('migration', null, InputOption::VALUE_NONE, 'Generate the migration file with the same name');
        $this->addOption('model-extends-pivot', null, InputOption::VALUE_NONE, 'The model extends Jmhc\Restful\Models\BasePivot');
        $this->addOption('model-extends-mongo', null, InputOption::VALUE_NONE, 'The model extends Jmhc\Restful\Models\BaseMongo');
        $this->addOption('controller-extends-custom', null, InputOption::VALUE_REQUIRED, 'The custom controller inherits its parent class', 'Jmhc\Restful\Controllers\BaseController');
        $this->addOption('model-extends-custom', null, InputOption::VALUE_REQUIRED, 'The custom model inherits its parent class', 'Jmhc\Restful\Models\BaseModel');
        $this->addOption('service-extends-custom', null, InputOption::VALUE_REQUIRED, 'The custom service inherits its parent class', 'Jmhc\Restful\Services\BaseService');
    }
}
