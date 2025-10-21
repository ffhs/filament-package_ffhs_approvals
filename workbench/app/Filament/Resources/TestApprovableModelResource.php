<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestApprovableModelResource\Pages\IndexTestApprovableModel;
use App\Filament\Resources\TestApprovableModelResource\Pages\ViewTestApprovableModel;
use App\Models\TestApprovableModels;
use Ffhs\Approvals\Filament\Actions\ApprovalActions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;

class TestApprovableModelResource extends Resource
{
    protected static ?string $model = TestApprovableModels::class;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $slug = 'approvable';

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema(
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
