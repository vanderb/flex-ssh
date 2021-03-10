<?php

namespace App\Commands;

use App\Services\ConfigService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use Illuminate\Support\Facades\Storage;

class ListAliases extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'ls {--alias= : get data for specific alias (optional)} {--host= : get all aliases by specified host (optional)}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List all custom SSH-Aliases.';

    /**
     * The table headers for the command.
     *
     * @var string[]
     */
    protected $headers = ['Alias', 'Host', 'User', 'Port'];

    public $sshConfigCollection;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ConfigService $config)
    {
        if (!app('installed')) {
            $this->warn('Required files can\'t be found. Please run install command:');
            $this->warn('php flex install');
            exit;
        }

        try {
            $client = app('ssh_disk');

            $aliases = $config->getAliases();

            if ($alias = $this->option('alias')) {
                if (!$config->aliasExists($alias)) {
                    $this->warn("Alias '{$alias}' does not exist.");
                    exit;
                }

                $this->table($this->headers, [$config->find($alias)->toArray()]);
                exit;
            }

            if ($host = $this->option('host')) {
                $this->table($this->headers, $config->findByHost($host)->toArray());
                exit;
            }

            $this->table($this->headers, $aliases->toArray());
        } catch (\Exception $e) {
            $this->warn('There are some errors in your ssh-alias-config.');
            $this->warn('Please verify each alias are declared in following way:');
            $this->warn("Host myalias\n  HostName myhost.name\n  User myuser\n  Port 22");
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
