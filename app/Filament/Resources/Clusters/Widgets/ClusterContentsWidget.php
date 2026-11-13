<?php

namespace App\Filament\Resources\Clusters\Widgets;

use App\Models\Content;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ClusterContentsWidget extends TableWidget
{
    public ?Model $record = null;

    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Content::query()
            ->with('category')
            ->where('cluster_id', $this->record?->id ?? 0)
            ->select(['id', 'title', 'status', 'is_pillar', 'cluster_position', 'category_id', 'published_at']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->heading(__('resource.cluster_resource.contents_widget.heading'))
            ->columns([
                TextColumn::make('title')
                    ->label(__('resource.content_resource.form.title'))
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('resource.content_resource.form.category'))
                    ->badge()
                    ->placeholder('—')
                    ->sortable(),
                IconColumn::make('is_pillar')
                    ->label(__('resource.cluster_resource.contents_widget.is_pillar'))
                    ->boolean()
                    ->trueIcon('heroicon-m-fire')
                    ->falseIcon('')
                    ->sortable(),
                TextColumn::make('cluster_position')
                    ->label(__('resource.content_resource.form.cluster_position'))
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('resource.content_resource.form.status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label(__('resource.content_resource.form.published_at'))
                    ->formatStateUsing(fn($state) => $state?->isoFormat('L'))
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
