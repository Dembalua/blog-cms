<?php

namespace App\Filament\Resources\Homepages;

use App\Enums\ContentType;
use App\Filament\Resources\Homepages\Pages\CreateHomepage;
use App\Filament\Resources\Homepages\Pages\EditHomepage;
use App\Filament\Resources\Homepages\Pages\ListHomepages;
use App\Filament\Resources\Homepages\Schemas\HomepageForm;
use App\Filament\Resources\Homepages\Tables\HomepagesTable;
use App\Models\Content;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class HomepageResource extends Resource
{
    protected static ?string $model = Content::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', ContentType::HOMEPAGE);
    }

    public static function form(Schema $schema): Schema
    {
        return HomepageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomepagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHomepages::route('/'),
            'create' => CreateHomepage::route('/create'),
            'edit' => EditHomepage::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('resource.homepage_resource.navigation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resource.homepage_resource.navigation.plural_model_label');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('resource.navigation_groups.system');
    }
}
