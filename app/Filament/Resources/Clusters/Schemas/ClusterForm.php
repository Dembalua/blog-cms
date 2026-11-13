<?php

namespace App\Filament\Resources\Clusters\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ClusterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resource.cluster_resource.form.section_title'))
                    ->description(__('resource.cluster_resource.form.section_description'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')
                            ->label(__('resource.cluster_resource.form.title'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(80),
                        Select::make('category_id')
                            ->label(__('resource.cluster_resource.form.category'))
                            ->relationship('category', 'name')
                            ->required()
                            ->native(false)
                            ->preload()
                            ->searchable(),
                        Select::make('relatedClusters')
                            ->label(__('resource.cluster_resource.form.related_clusters'))
                            ->helperText(__('resource.cluster_resource.form.related_clusters_helper'))
                            ->multiple()
                            ->relationship(
                                name: 'relatedClusters',
                                titleAttribute: 'title',
                                modifyQueryUsing: fn(Builder $query, ?Model $record) => $query->when(
                                    $record?->id,
                                    fn($q, $id) => $q->where('clusters.id', '!=', $id)
                                ),
                            )
                            ->preload()
                            ->searchable()
                            ->maxItems(3),
                    ]),
            ]);
    }
}
