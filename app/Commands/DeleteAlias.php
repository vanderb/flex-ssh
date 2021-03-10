<?php

namespace App\Commands;

use App\Services\ConfigService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class DeleteAlias extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'delete {alias=: alias name (required)}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Remove alias from alias-config';

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

        if (!$config->aliasExists($alias)) {
            $this->warn("Alias '{$alias}' does not exist.");
            exit;
        }

        if ($confirmed = $this->confirm("Do you realy want to remove alias '{$alias}'? This cannot be undone.", false)) {
            $config->delete($alias);
            $this->info("'{$alias}' was removed from alias-list.");
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
