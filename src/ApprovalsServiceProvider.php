<?php

namespace Ffhs\Approvals;

use Ffhs\Approvals\Policies\ApprovalByPolicy;
use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ApprovalsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-package_ffhs_approvals';
    public static string $viewNamespace = 'filament-package_ffhs_approvals';


    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations();
            });

        $configFileName = $package->shortName();
        $package->hasTranslations();
        $package->hasMigrations($this->getMigrations());
        $package->hasViews(static::$viewNamespace);

        if (file_exists($package->basePath('/../config/' . $configFileName . '.php'))) {
            $package->hasConfigFile();
        }
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_filament_package_ffhs_approvals_table',
        ];
    }

    public function packageRegistered(): void
    {
    }

    public function boot(): self
    {
        parent::boot();

        Gate::define('can_approve_by', [ApprovalByPolicy::class, 'approve']);

        return $this;
    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path('stubs/filament-package_ffhs_approvals/' . $file->getFilename()),
                ], 'filament-package_ffhs_approvals-stubs');
            }
        }

        // Testing
        // Testable::mixin(new TestsApprovals());
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [];
    }

    protected function getAssetPackageName(): ?string
    {
        return 'ffhs/filament-package_ffhs_approvals';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }
}
