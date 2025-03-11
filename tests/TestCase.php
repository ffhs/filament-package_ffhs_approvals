<?php

namespace Ffhs\Approvals\Tests;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
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
//        $loader = AliasLoader::getInstance();
//        $migration = include __DIR__.'/../workbench/database/migrations/0_create_test_approvable_model.php';
//        $migration->up();
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

//class TestCase extends Orchestra
//{
//
//    protected function setUp(): void
//    {
//        parent::setUp();
//
//        Factory::guessFactoryNamesUsing(
//            fn (string $modelName) => 'Ffhs\\Approvals\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
//        );
//    }
//
//    protected function getPackageProviders($app)
//    {
//        return [
//            ActionsServiceProvider::class,
//            BladeCaptureDirectiveServiceProvider::class,
//            BladeHeroiconsServiceProvider::class,
//            BladeIconsServiceProvider::class,
//            FilamentServiceProvider::class,
//            FormsServiceProvider::class,
//            InfolistsServiceProvider::class,
//            LivewireServiceProvider::class,
//            NotificationsServiceProvider::class,
//            SupportServiceProvider::class,
//            TablesServiceProvider::class,
//            WidgetsServiceProvider::class,
//            ApprovalsServiceProvider::class,
//        ];
//    }
//
//    public function getEnvironmentSetUp($app)
//    {
//        config()->set('database.default', 'testing');
//
//        /*
//        $migration = include __DIR__.'/../database/migrations/create_filament-package_ffhs_approvals_table.php.stub';
//        $migration->up();
//        */
//    }
//}
