<?php
/**
 * User: YL
 * Date: 2019/10/18
 */

namespace Jmhc\Restful\Console\Commands;

use Hyperf\Contract\ConfigInterface;
use Hyperf\DB\DB;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Str;
use Symfony\Component\Console\Input\InputOption;
use Throwable;

/**
 * 生成公用模型
 * @package Jmhc\Restful\Console\Commands
 */
class MakeCommonModelCommand extends AbstractMakeCommand
{
    /**
     * 命令名称
     * @var string
     */
    protected $name = 'jmhc-api:make-common-model';

    /**
     * 命令描述
     * @var string
     */
    protected $description = 'Generate the common model files';

    /**
     * 默认保存路径
     * @var string
     */
    protected $defaultDir = 'Common/Models/';

    /**
     * 配置数据库
     * @var string
     */
    protected $configDatabase;

    /**
     * 配置数据表前缀
     * @var string
     */
    protected $configPrefix;

    /**
     * 参数 db
     * @var string
     */
    protected $optionDb;

    /**
     * 参数 prefix
     * @var string
     */
    protected $optionPrefix;

    /**
     * 参数 table
     * @var array
     */
    protected $optionTable;

    /**
     * 参数 dir
     * @var string
     */
    protected $optionDir;

    /**
     * 参数 force
     * @var bool
     */
    protected $optionForce;

    /**
     * 参数 clean
     * @var bool
     */
    protected $optionClear;

    /**
     * 参数 suffix
     * @var bool
     */
    protected $optionSuffix;

    public function __construct(string $name = null)
    {
        // 配置实例
        $configInterface = ApplicationContext::getContainer()->get(ConfigInterface::class);

        // 配置数据表
        $this->configDatabase = $configInterface->get('database.default.database');
        // 配置数据表前缀
        $this->configPrefix = $configInterface->get('database.default.prefix');

        parent::__construct($name);
    }

    /**
     * 主要操作
     */
    protected function mainHandle()
    {
        try {
            // 获取所有表
            $tables = $this->getTables($this->optionDb);

            // 清除所有
            if ($this->optionClear) {
                $this->clearAll($tables);
                return true;
            }

            // 生成模型
            foreach ($tables as $table) {
                if (in_array($table, $this->optionTable)) {
                    continue;
                }
                $this->buildModel($table);
            }
        } catch (Throwable $e) {
            $this->error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }

        return true;
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
     * 清除所有模型
     * @param array $tables
     */
    protected function clearAll(array $tables)
    {
        // 清除确认
        $this->confirm('Confirm delete all models?', false);

        $files = glob($this->dir . '*.php');
        $tables = array_map(function ($v) {
            return $this->getBuildName($this->optionPrefix, $v) . '.php';
        }, $tables);

        foreach ($files as $file) {
            if (! in_array(basename($file), $tables)) {
                continue;
            }

            unlink($file);
            $this->info($file . ' delete Succeed!');
        }
    }

    /**
     * 获取所有数据表
     * @param string $database
     * @return array
     */
    protected function getTables(string $database = '')
    {
        $sql = ! empty($database) ? 'SHOW TABLES FROM ' . $database : 'SHOW TABLES ';

        $result = DB::query($sql);
        $info   = [];
        foreach ($result as $key => $val) {
            $info[$key] = current($val);
        }

        return $info;
    }

    /**
     * 生成模型文件
     * @param string $table
     * @return bool
     */
    protected function buildModel(string $table)
    {
        $name = $this->getBuildName($this->optionPrefix, $table);
        $filePath = $this->dir . $name . '.php';

        if (file_exists($filePath) && ! $this->optionForce) {
            return false;
        }

        $content = $this->getBuildContent($name);
        file_put_contents($filePath, $content);

        $this->info($filePath . ' create Succeed!');
        return true;
    }

    /**
     * 获取生成名称
     * @param string $prefix
     * @param string $table
     * @return string
     */
    protected function getBuildName(string $prefix, string $table)
    {
        return Str::studly(Str::singular(str_replace($prefix, '', $table))) . ($this->optionSuffix ? 'Model' : '');
    }

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
     * 设置参数、选项
     */
    protected function setArgumentOption()
    {
        // 命令选项
        $this->optionDb = $this->option('db') ?? $this->configDatabase;
        $this->optionPrefix = $this->option('prefix') ?? $this->configPrefix;
        $this->optionTable = array_map(function ($v) {
            return $this->optionPrefix . str_replace($this->optionPrefix, '', $v);
        }, $this->option('table'));;
        $this->optionDir = $this->option('dir');
        $this->optionForce = $this->option('force');
        $this->optionClear = $this->option('clear');
        $this->optionSuffix = $this->option('suffix');
    }

    /**
     * 获取选项
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['db', 'd', InputOption::VALUE_REQUIRED, 'Model source database', $this->configDatabase],
            ['prefix', 'p', InputOption::VALUE_REQUIRED, 'Data table prefix', $this->configPrefix],
            ['table', 't', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Exclude table names'],
            ['dir', null, InputOption::VALUE_REQUIRED, 'File saving path, relative to app directory', $this->defaultDir],
            ['force', 'f', InputOption::VALUE_NONE, 'Overwrite existing files'],
            ['clear', 'c', InputOption::VALUE_NONE, 'Clear all model files'],
            ['suffix', 's', InputOption::VALUE_NONE, 'Add the `Model` suffix'],
        ];
    }
}
