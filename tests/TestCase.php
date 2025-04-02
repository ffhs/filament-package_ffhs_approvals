<?php

namespace Ffhs\Approvals\Tests;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;

#[WithMigration]
abstract class TestCase extends Orchestra
{
    use WithWorkbench;
    use RefreshDatabase;

    protected $enablesPackageDiscoveries = true;
    protected string $seeder = DatabaseSeeder::class;

    protected function getEnvironmentSetUp($app)
    {
        $loader = AliasLoader::getInstance();
        $migration = include __DIR__ . '/../workbench/database/migrations/0_create_test_approvable_model.php';
        $migration->up();
    }


    protected function setUp(): void
    {
        // Code before application created.
        $this->afterApplicationCreated(function () {
            Artisan::call('filament:assets');

            Filament::setCurrentPanel(
                Filament::getPanel('admin'), // Where `app` is the ID of the panel you want to test.
            );
        });

        parent::setUp();

        $this->actingAs(User::query()->first());
    }
}
