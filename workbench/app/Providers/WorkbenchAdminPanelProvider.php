<?php

namespace App\Providers;

use App\Filament\Resources\TestApprovableModelResource;
use Ffhs\Approvals\ApprovalsPlugin;
use Ffhs\FilamentPackageFfhsCustomForms\CustomFormPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class WorkbenchAdminPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
       return $panel
           ->default()
           ->login()
           ->id('admin')
           ->path('admin')
           ->resources([
               TestApprovableModelResource::class
           ])
           ->middleware([
               EncryptCookies::class,
               AddQueuedCookiesToResponse::class,
               StartSession::class,
               AuthenticateSession::class,
               ShareErrorsFromSession::class,
               VerifyCsrfToken::class,
               SubstituteBindings::class,
               DisableBladeIconComponents::class,
               DispatchServingFilamentEvent::class,
               //Localization::class,
           ])
           ->authMiddleware([
               Authenticate::class,
           ])
           ->plugins([
               ApprovalsPlugin::make(),
//               SpatieLaravelTranslatablePlugin::make(),
           ]);
    }
}
