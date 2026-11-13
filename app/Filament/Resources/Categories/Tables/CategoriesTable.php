<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Models\MenuItem;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('resource.default_form_fields.name'))
                    ->sortable()
                    ->searchable(),
                CuratorColumn::make('image_id')
                    ->label(__('resource.default_form_fields.image'))
                    ->circular()
                    ->imageSize(40),
                IconColumn::make('active')
                    ->sortable()
                    ->label(__('resource.category_resource.form.active'))
                    ->boolean(),
                TextColumn::make('metaData.robots')
                    ->label(__('meta-data.fields.robots'))
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->striped()
            ->paginated([10, 25, 50, 100, 'all'])
            ->reorderable('sort')
            ->defaultSort('sort')
            ->filters([
                SelectFilter::make('active')
                    ->label(__('resource.category_resource.form.active'))
                    ->options([
                        '1' => __('resource.default_form_fields.active'),
                        '0' => __('resource.default_form_fields.inactive'),
                    ]),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->deferFilters(false)
            ->filtersFormColumns(3)
            ->filtersFormSchema(fn (array $filters): array => [
                Section::make()
                    ->schema([
                        $filters['active'],
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (DeleteBulkAction $action, \Illuminate\Database\Eloquent\Collection $records) {
                            $blockedByMenu = $records->filter(
                                fn($record) => MenuItem::where('model_type', \App\Models\Category::class)->where('model_id', $record->id)->exists()
                            );

                            $blockedByContents = $records->filter(
                                fn($record) => $record->contents()->exists()
                            );

                            $blocked = $blockedByMenu->merge($blockedByContents)->unique('id');

                            if ($blocked->isNotEmpty()) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('resource.category_resource.delete_blocked_title'))
                                    ->body($blocked->pluck('name')->join(', '))
                                    ->send();

                                $action->halt();
                            }
                        }),
                ]),
            ]);
    }
}
