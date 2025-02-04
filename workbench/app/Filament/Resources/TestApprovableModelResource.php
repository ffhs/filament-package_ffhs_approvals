<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestApprovableModelResource\Pages\IndexTestApprovableModel;
use App\Filament\Resources\TestApprovableModelResource\Pages\ViewTestApprovableModel;
use App\Models\TestApprovableModels;
use Ffhs\Approvals\Infolists\Actions\ApprovalActions;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;

class TestApprovableModelResource extends Resource
{
    protected static ?string $model = TestApprovableModels::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $slug = 'approvable';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema(
            [
                ApprovalActions::make('test-key-1')
            ]
        );
    }

    public static function getPages(): array
    {
        return [
            'index' => IndexTestApprovableModel::route('/'),
            'view' => ViewTestApprovableModel::route('/{record}'),
        ];
    }
}
