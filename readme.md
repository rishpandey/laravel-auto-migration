# Laravel Automatic Migrations

All credits to Kevin Dion, as this was initially a part of [swift](https://github.com/redbastie/swift).

### Installation

    composer require rishpandey/laravel-auto-migration

### Usage

In order to use automatic migrations, simply specify a `migration` method in your model:

    class Lead extends Model
    {
        use SwiftModel;

        public function migration(Blueprint $table)
        {
            $table->id();
            $table->string('name');
            $table->timestamps();
        }

Now run the automatic migration command:

    php artisan migrate:auto

The package uses Doctrine DBAL in order to diff the existing model table and make the necessary changes to it. If the table does not exist, it will create it.

By default the package looks under "App\Models" for your models, but you can pass a `--base` to specify that models exists under "App":

    php artisan migrate:auto --base

You can also pass `--fresh` and/or `--seed` to the `migrate:auto` command in order to get fresh migrations and/or run your seeders afterwards:

    php artisan migrate:auto --fresh --seed

If your app contains traditional migrations in the `database/migrations` folder, they will be handled before the automatic migrations.
