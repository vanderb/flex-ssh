<?php

namespace App\Commands;

use App\Services\ConfigService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class MakeAlias extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'make {alias : alias name (required)} {--q= : quick notation} {--host= : host name (required} {--user= : user name (optional)} {--port= : port of ssh connection (optional)}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create new SSH-Alias';

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

        $alias = $this->argument('alias');

        if (app('alias')->aliasExists($alias)) {
            $this->warn("Alias '{$alias}' already exists.");
            exit;
        }

        $q = $this->option('q');
        if (str_contains($q, '@')) {
            list($user, $host) = explode('@', $q);
            if (str_contains($q, ':')) {
                list($host, $port) = explode(':', $q);
            }
        }

        if (!$host) {
            $host = $this->option('host') ?: $this->ask('Host');

            if (!$host) {
                $this->warn('Please define a host.');
                exit;
            }
        }

        if (!$user) {
            $user = $this->option('user') ?: $this->ask('User');
            if (!$user) {
                $this->warn('Please define a user.');
                exit;
            }
        }

        if (!isset($port)) {
            $port = $this->option('port') ?: $this->ask('Port', 22);
        }

        $aliasConfig = collect([
            'Host' => $alias,
            'HostName' => $host,
            'User' => $user,
            'Port' => $port
        ]);

        if ($appended = $config->createNewAlias($aliasConfig)) {
            $this->info("Alias '{$alias}' was created.");
            $this->notify("Hello Web Artisan", "Love beautiful..", "icon.png");
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
