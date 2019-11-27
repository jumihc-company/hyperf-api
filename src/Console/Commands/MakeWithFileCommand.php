<?php
/**
 * User: YL
 * Date: 2019/11/18
 */

namespace Jmhc\Restful\Console\Commands;

use Hyperf\Command\Command;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Str;
use Jmhc\Restful\Console\Commands\Traits\CommandTrait;
use Jmhc\Restful\Console\Commands\Traits\MakeTrait;
use Symfony\Component\Console\Input\InputOption;

/**
 * 通过关联文件生成
 * @package Jmhc\Restful\Console\Commands
 */
class MakeWithFileCommand extends Command
{
    use CommandTrait;
    use MakeTrait;

    /**
     * 配置实例
     * @var ConfigInterface
     */
    protected $configInterface;

    /**
     * 命令名称
     * @var string
     */
    protected $name = 'jmhc-api:make-with-file';

    /**
     * 命令描述
     * @var string
     */
    protected $description = 'Generate some file with file';

    /**
     * 默认保存路径
     * @var string
     */
    protected $defaultDir = 'Http/';

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
     * 选项 suffix
     * @var string
     */
    protected $optionSuffix;

    /**
     * 选项 controller
     * @var string
     */
    protected $optionController;

    /**
     * 是否覆盖控制器
     * @var bool
     */
    protected $isForceController;

    /**
     * 选项 service
     * @var string
     */
    protected $optionService;

    /**
     * 是否覆盖服务
     * @var bool
     */
    protected $isForceService;

    /**
     * 选项 model
     * @var string
     */
    protected $optionModel;

    /**
     * 是否覆盖模型
     * @var bool
     */
    protected $isForceModel;

    /**
     * 选项 migration
     * @var string
     */
    protected $optionMigration;

    /**
     * 选项 model_extends_pivot
     * @var bool
     */
    protected $optionModelExtendsPivot;

    /**
     * 选项 model_extends_mongo
     * @var bool
     */
    protected $optionModelExtendsMongo;

    public function __construct(string $name = null)
    {
        $this->setDescription($this->description);
        $this->configInterface = ApplicationContext::getContainer()->get(ConfigInterface::class);

        parent::__construct($name);
    }

    public function handle()
    {
        // 设置参数、选项
        $this->setArgumentOption();

        // 读取生成文件配置
        $tables = $this->configInterface->get('jmhc-build-file', []);

        // 数据表不存在
        if (empty($tables)) {
            // 运行完成
            $this->runComplete();
        }

        // 过滤名称
        $tables = $this->filterTables($tables);

        // 生成文件
        foreach ($tables as $table) {
            $this->buildFile($table);
        }

        // 运行完成
        $this->runComplete();
    }

    /**
     * 过滤表名
     * @param array $tables
     * @return array
     */
    protected function filterTables(array $tables)
    {
        // 数据表前缀
        $prefix = $this->configInterface->get('databases.default.prefix', '');

        return array_values(array_filter(array_unique(array_map(function ($table) use ($prefix) {
            return str_replace($prefix, '', $table);
        }, $tables))));
    }

    /**
     * 创建文件
     * @param string $name
     */
    protected function buildFile(string $name)
    {
        // 命令参数
        $arguments = [
            'name' => $name,
            '--module' => $this->optionModule,
            '--suffix' => $this->optionSuffix,
            '--model-extends-pivot' => $this->optionModelExtendsPivot,
            '--model-extends-mongo' => $this->optionModelExtendsMongo,
        ];

        // 创建控制器
        if ($this->optionController) {
            $arguments['--force'] = $this->isForceController;
            $arguments['--dir'] = $this->optionDir . 'Controllers/';
            $this->call('jmhc-api:make-controller', $arguments);
        }

        // 创建模型
        if ($this->optionModel) {
            $arguments['--force'] = $this->isForceModel;
            $arguments['--dir'] = $this->optionDir . 'Models/';
            $this->call('jmhc-api:make-model', $arguments);
        }

        // 创建服务
        if ($this->optionService) {
            $arguments['--force'] = $this->isForceService;
            $arguments['--dir'] = $this->optionDir . 'Services/';
            $this->call('jmhc-api:make-service', $arguments);
        }

        // 创建迁移
        if ($this->optionMigration) {
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
        $this->optionDir = $this->filterOptionDir($this->option('dir'));
        $this->optionModule = $this->option('module') ?? '';
        $this->optionSuffix = $this->option('suffix');
        $this->optionController = $this->option('controller');
        $this->isForceController = $this->option('force') || $this->option('force-controller');
        $this->optionService = $this->option('service');
        $this->isForceService = $this->option('force') || $this->option('force-service');
        $this->optionModel = $this->option('model');
        $this->isForceModel = $this->option('force') || $this->option('force-model');
        $this->optionMigration = $this->option('migration');
        $this->optionModelExtendsPivot = $this->option('model-extends-pivot');
        $this->optionModelExtendsMongo = $this->option('model-extends-mongo');
    }

    /**
     * 命令配置
     */
    protected function configure()
    {
        $this->addOption('dir', null, InputOption::VALUE_REQUIRED, 'File saving path, relative to app directory', $this->defaultDir);
        $this->addOption('module', 'm', InputOption::VALUE_REQUIRED, 'Module name');
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing file');
        $this->addOption('force-controller', null, InputOption::VALUE_NONE, 'Overwrite existing controller file');
        $this->addOption('force-service', null, InputOption::VALUE_NONE, 'Overwrite existing service file');
        $this->addOption('force-model', null, InputOption::VALUE_NONE, 'Overwrite existing model file');
        $this->addOption('suffix', 's', InputOption::VALUE_NONE, sprintf('Add suffix'));
        $this->addOption('controller', null, InputOption::VALUE_NONE, 'Generate the controller file with the same name');
        $this->addOption('service', null, InputOption::VALUE_NONE, 'Generate the service file with the same name');
        $this->addOption('model', null, InputOption::VALUE_NONE, 'Generate the model file with the same name');
        $this->addOption('migration', null, InputOption::VALUE_NONE, 'Generate the migration file with the same name');
        $this->addOption('model-extends-pivot', null, InputOption::VALUE_NONE, 'The model extends Jmhc\Restful\Models\BasePivot');
        $this->addOption('model-extends-mongo', null, InputOption::VALUE_NONE, 'The model extends Jmhc\Restful\Models\BaseMongo');
    }
}
