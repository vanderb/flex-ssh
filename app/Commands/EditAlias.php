<?php

namespace App\Commands;

use App\Services\ConfigService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Config;
use LaravelZero\Framework\Commands\Command;

class EditAlias extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'update {alias : alias name (required)}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Edit existing alias';

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

            $alias = $this->argument('alias');

            if (!$config->aliasExists($alias)) {
                $this->warn("Alias '{$alias}' does not exist.");
                exit;
            }

            $alias = $config->find($alias);

            $originAlias = $alias->get('Host');

            $alias->put('Host', $this->askForHost($alias));
            $alias->put('HostName', $this->ask('Enter Host', $alias->get('HostName')));
            $alias->put('User', $this->ask('Enter User', $alias->get('User')));
            $alias->put('Port', $this->ask('Enter Port', $alias->get('Port')));

            $config->update($originAlias, $alias);

            $this->info('Alias was updated.');
        } catch (\Exception $e) {

            dd($e->getMessage());

            $this->warn('There are some errors in your ssh-alias-config.');
            $this->warn('Please verify each alias are declared in following way:');
            $this->warn("Host myalias\n  HostName myhost.name\n  User myuser\n  Port 22");
        }
    }

    protected function askForHost($alias)
    {
        $host = $this->ask('Enter Alias', $alias->get('Host'));
        if (app('alias')->aliasExists($host) && $host !== $alias->get('Host')) {
            $this->warn('Alias already exists, please enter different alias');
            return $this->askForHost($alias);
        }
        return $host;
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
