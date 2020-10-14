<?php

namespace RishPandey\LaravelAutoMigration\Providers;

use Illuminate\Support\ServiceProvider;
use RishPandey\LaravelAutoMigration\Commands\MigrateAutoCommand;

class AutoMigrationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MigrateAutoCommand::class,
            ]);
        }
    }

    public function register()
    {
        //
    }
}
