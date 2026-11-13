<?php

namespace App\Filament\Resources\Homepages\Tables;

use App\Models\Setting;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HomepagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('resource.homepage_resource.form.title'))
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                IconColumn::make('is_active')
                    ->label(__('resource.homepage_resource.table.active'))
                    ->getStateUsing(fn ($record) => (int) Setting::get('site.homepage_content') === (int) $record->id)
                    ->boolean()
                    ->tooltip(fn ($state) => $state ? __('resource.homepage_resource.table.active_badge') : null),
                TextColumn::make('author.name')
                    ->label(__('resource.homepage_resource.form.author'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('published_at')
                    ->label(__('resource.homepage_resource.form.published_at'))
                    ->formatStateUsing(fn ($state) => $state?->isoFormat('L'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('revised_at')
                    ->label(__('resource.homepage_resource.form.revised_at'))
                    ->formatStateUsing(fn ($state) => $state?->isoFormat('L'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('resource.default_form_fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->striped()
            ->defaultSort('updated_at', 'desc')
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
