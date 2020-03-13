<?php
/**
 * User: YL
 * Date: 2020/03/13
 */

namespace Jmhc\Restful\Console\Commands;

use Exception;
use Hyperf\Command\Command;
use Jmhc\Restful\Console\Commands\Traits\CommandTrait;
use Symfony\Component\Console\Input\InputOption;

class DownCommand extends Command
{
    use CommandTrait;

    const FILE_NAME = 'runtime/down.cache';

    protected $name = 'jmhc-api:down';

    protected $description = 'Put the application into maintenance mode';

    public function __construct(string $name = null)
    {
        $this->setDescription($this->description);

        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    public function handle()
    {
        try {
            if (file_exists(base_path(static::FILE_NAME))) {
                $this->comment('Application is already down.');

                return 1;
            }

            file_put_contents(base_path(static::FILE_NAME),
                json_encode($this->getDownFilePayload(),
                    JSON_PRETTY_PRINT));

            $this->comment('Application is now in maintenance mode.');
        } catch (Exception $e) {
            $this->error('Failed to enter maintenance mode.');

            $this->error($e->getMessage());

            return 1;
        }

        return 0;
    }

    /**
     * 获取文件内容
     * @return array
     */
    protected function getDownFilePayload()
    {
        return [
            'time' => time(),
            'message' => $this->option('message'),
            'allowed' => $this->option('allow'),
        ];
    }

    protected function configure()
    {
        $this->addOption('message', null, InputOption::VALUE_OPTIONAL, 'The message for the maintenance mode');
        $this->addOption('allow', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'IP or networks allowed to access the application while in maintenance mode');
    }
}
