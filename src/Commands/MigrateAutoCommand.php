<?php

namespace RishPandey\LaravelAutoMigration\Commands;

use Doctrine\DBAL\Schema\Comparator;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class MigrateAutoCommand extends Command
{
    protected $signature = 'migrate:auto {--fresh} {--seed}';

    public function handle()
    {
        Artisan::call('migrate' . ($this->option('fresh') ? ':fresh' : '') . ' --force');

        if ($existsAtBase = $this->option('base')) {
            $modelsPath = app_path();
        } else {
            $modelsPath = app_path('Models');
        }

        foreach ((new Filesystem)->allFiles($modelsPath) as $file) {
            $className = 'App\\' . ($existsAtBase ? '\\Models\\' : '') .
                str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname());
            $class = app($className);

            if (method_exists($class, 'migration')) {
                if (Schema::hasTable($class->getTable())) {
                    $tempTable = 'temp_' . $class->getTable();

                    Schema::dropIfExists($tempTable);
                    Schema::create($tempTable, function (Blueprint $table) use ($class) {
                        $class->migration($table);
                    });

                    $schemaManager = $class->getConnection()->getDoctrineSchemaManager();
                    $classTableDetails = $schemaManager->listTableDetails($class->getTable());
                    $tempTableDetails = $schemaManager->listTableDetails($tempTable);
                    $tableDiff = (new Comparator)->diffTable($classTableDetails, $tempTableDetails);

                    if ($tableDiff) {
                        $schemaManager->alterTable($tableDiff);
                    }

                    Schema::drop($tempTable);
                } else {
                    Schema::create($class->getTable(), function (Blueprint $table) use ($class) {
                        $class->migration($table);
                    });
                }
            }
        }

        $this->info('Migration complete!');

        if ($this->option('seed')) {
            Artisan::call('db:seed');

            $this->info('Seeding complete!');
        }
    }
}
