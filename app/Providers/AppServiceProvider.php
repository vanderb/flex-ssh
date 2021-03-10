<?php

namespace App\Providers;

use App\Services\ConfigService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ssh_disk', function () {
            return Storage::createLocalDriver(['root' => env('HOME') . '/.ssh']);
        });

        $this->app->singleton('installed', function () {
            $client = Storage::createLocalDriver(['root' => env('HOME') . '/.ssh']);
            $sshConfig = $client->get('config');
            return $client->exists('ssh_alias') && strpos($sshConfig, 'Include ~/.ssh/ssh_alias') !== false;
        });

        $this->app->singleton('alias', function () {
            return resolve(ConfigService::class);
        });
    }
}
