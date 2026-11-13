<?php

namespace App\Filament\Resources\Contents\Tables;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Models\Category;
use App\Models\Cluster;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('featureImage')
                    ->label(__('resource.content_resource.form.feature_image'))
                    ->circular()
                    ->imageSize(40)
                    ->toggleable(),
                TextColumn::make('title')
                    ->label(__('resource.content_resource.form.title'))
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->title),
                TextColumn::make('cluster.title')
                    ->label(__('resource.content_resource.form.cluster'))
                    ->getStateUsing(fn ($record) => $record->cluster?->title ?? __('resource.content_resource.filters.no_cluster'))
                    ->badge()
                    ->color(fn ($state) => $state === __('resource.content_resource.filters.no_cluster') ? 'warning' : 'gray')
                    ->sortable(query: fn ($query, $direction) => $query->orderBy(
                        Cluster::select('title')->whereColumn('clusters.id', 'contents.cluster_id'),
                        $direction
                    ))
                    ->toggleable(),
                TextColumn::make('cluster_position')
                    ->label(__('resource.content_resource.form.cluster_position'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('type')
                    ->label(__('resource.content_resource.form.type'))
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('author.name')
                    ->label(__('resource.content_resource.form.author'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('published_at')
                    ->label(__('resource.content_resource.form.published_at'))
                    ->formatStateUsing(fn ($state) => $state?->isoFormat('L'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('revised_at')
                    ->label(__('resource.content_resource.form.revised_at'))
                    ->formatStateUsing(fn ($state) => $state?->isoFormat('L'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label(__('resource.content_resource.form.status'))
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('path.url')
                    ->label(__('resource.content_resource.form.path'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_featured')
                    ->label(__('resource.content_resource.form.is_featured'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('category.name')
                    ->label(__('resource.content_resource.form.category'))
                    ->getStateUsing(fn ($record) => $record->category?->name ?? __('resource.content_resource.filters.no_category'))
                    ->badge()
                    ->color(fn ($state) => $state === __('resource.content_resource.filters.no_category') ? 'warning' : 'gray')
                    ->sortable(query: fn ($query, $direction) => $query->orderBy(
                        Category::select('name')->whereColumn('categories.id', 'contents.category_id'),
                        $direction
                    ))
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_pillar')
                    ->label(__('resource.content_resource.form.is_pillar'))
                    ->default(false)
                    ->icon(fn ($state) => $state ? 'heroicon-s-fire' : 'heroicon-o-fire')
                    ->color(fn ($state) => $state ? 'warning' : 'gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label(__('resource.content_resource.form.type'))
                    ->options(ContentType::class)
                    ->native(false),
                SelectFilter::make('status')
                    ->label(__('resource.content_resource.form.status'))
                    ->options(ContentStatus::class)
                    ->native(false),
                SelectFilter::make('author_id')
                    ->label(__('resource.content_resource.form.author'))
                    ->relationship('author', 'name')
                    ->native(false),
                Filter::make('is_featured')
                    ->label(__('resource.content_resource.form.is_featured'))
                    ->query(fn ($query) => $query->where('is_featured', true))
                    ->toggle(),
                Filter::make('no_category')
                    ->label(__('resource.content_resource.filters.no_category'))
                    ->query(fn ($query) => $query->whereNull('category_id'))
                    ->toggle(),
                Filter::make('no_cluster')
                    ->label(__('resource.content_resource.filters.no_cluster'))
                    ->query(fn ($query) => $query->whereNull('cluster_id'))
                    ->toggle(),
                SelectFilter::make('category_id')
                    ->label(__('resource.content_resource.form.category'))
                    ->relationship('category', 'name')
                    ->native(false),
                SelectFilter::make('cluster_id')
                    ->label(__('resource.content_resource.form.cluster'))
                    ->relationship('cluster', 'title')
                    ->native(false),
                Filter::make('is_pillar')
                    ->label(__('resource.content_resource.form.is_pillar'))
                    ->query(fn ($query) => $query->whereNotNull('is_pillar'))
                    ->toggle(),
                Filter::make('published_at')
                    ->schema([
                        DatePicker::make('published_from')
                            ->label(__('resource.content_resource.form.published_from'))
                            ->native(false)
                            ->maxDate(fn (Get $get) => $get('published_until') ?: now()),
                        DatePicker::make('published_until')
                            ->label(__('resource.content_resource.form.published_until'))
                            ->native(false)
                            ->minDate(fn (Get $get) => $get('published_from')),
                    ])
                    ->columns(2)
                    ->columnSpan(2)
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['published_from'], fn ($query, $date) => $query->whereDate('published_at', '>=', $date))
                            ->when($data['published_until'], fn ($query, $date) => $query->whereDate('published_at', '<=', $date));
                    }),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->deferFilters(false)
            ->filtersFormColumns(4)
            ->filtersFormSchema(fn (array $filters): array => [
                Section::make()
                    ->schema([
                        $filters['type'],
                        $filters['status'],
                        $filters['author_id'],
                        $filters['category_id'],
                        $filters['cluster_id'],
                        $filters['published_at'],
                        $filters['is_featured']->columnStart(1),
                        $filters['no_category'],
                        $filters['no_cluster'],
                        $filters['is_pillar'],
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
            ])
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
