<?php

namespace AdamTyn\Lumen\Artisan;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\InteractsWithTime;

class DownCommand extends Command
{
    use InteractsWithTime;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'down {--message= : The message for the maintenance mode}
                                 {--retry= : The number of seconds after which the request may be retried}
                                 {--allow=* : IP or networks allowed to access the application while in maintenance mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '让 Lumen 应用进入维护模式';

    /**
     * @var string
     */
    protected $path;

    /**
     * CheckForMaintenanceMode constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->path = storage_path('framework/down');
    }

    public function handle()
    {
        try {
            if (file_exists($this->path)) {
                $this->comment('Application is already down.');

                return;
            }

            file_put_contents($this->path, json_encode($this->getDownFilePayload(), JSON_PRETTY_PRINT));

            $this->comment('Application is now in maintenance mode.');
        } catch (Exception $e) {
            $this->error('Failed to enter maintenance mode.');

            $this->error($e->getMessage());

            return;
        }
    }

    /**
     * Get the payload to be placed in the "down" file.
     *
     * @return array
     */
    protected function getDownFilePayload()
    {
        return [
            'time' => $this->currentTime(),
            'message' => $this->option('message'),
            'retry' => $this->getRetryTime(),
            'allowed' => $this->option('allow'),
        ];
    }

    /**
     * Get the number of seconds the client should wait before retrying their request.
     *
     * @return int|null
     */
    protected function getRetryTime()
    {
        $retry = $this->option('retry');

        return is_numeric($retry) && $retry > 0 ? (int)$retry : null;
    }
}
