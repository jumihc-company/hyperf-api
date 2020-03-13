<?php
/**
 * User: YL
 * Date: 2020/03/13
 */

namespace Jmhc\Restful\Console\Commands;

use Exception;
use Hyperf\Command\Command;

class UpCommand extends Command
{
    const FILE_NAME = 'runtime/down.cache';

    protected $name = 'jmhc-api:up';

    protected $description = 'Bring the application out of maintenance mode';

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
            if (! file_exists(base_path(static::FILE_NAME))) {
                $this->comment('Application is already up.');

                return 1;
            }

            unlink(base_path(static::FILE_NAME));

            $this->info('Application is now live.');
        } catch (Exception $e) {
            $this->error('Failed to disable maintenance mode.');

            $this->error($e->getMessage());

            return 1;
        }

        return 0;
    }
}
