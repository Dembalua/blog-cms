<?php

namespace App\Filament\Resources\Clusters\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClustersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('resource.cluster_resource.form.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('resource.cluster_resource.form.category'))
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label(__('resource.default_form_fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('resource.default_form_fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label(__('resource.cluster_resource.form.category'))
                    ->relationship('category', 'name')
                    ->native(false)
                    ->preload(),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->deferFilters(false)
            ->filtersFormColumns(4)
            ->filtersFormSchema(fn (array $filters): array => [
                Section::make()
                    ->schema([
                        $filters['category_id'],
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
            ])
            ->striped()
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
