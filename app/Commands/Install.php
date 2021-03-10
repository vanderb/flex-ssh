<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use Illuminate\Support\Facades\Storage;

class Install extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'install';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Install required ssh-alias files (if not exist).';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = app('ssh_disk');

        // Generate ssh-alias file if not exists.
        if (!$client->exists('ssh_alias')) {
            $client->put('ssh_alias', '');
            $this->info('SSH-Alias file was created.');
        }

        // Include ssh-alias file to ssh-config if not already included
        $sshConfig = $client->get('config');
        if (strpos($sshConfig, 'Include ~/.ssh/ssh_alias') === false) {
            $sshConfig .= "\n\n" . "Include ~/.ssh/ssh_alias";
            $client->put('config', $sshConfig);
            $this->info('SSH-Alias file was included to ssh-config.');
        }

        $this->info('Installation process was successfull.');
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
