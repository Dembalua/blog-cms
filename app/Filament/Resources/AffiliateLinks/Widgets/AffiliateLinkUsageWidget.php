<?php

namespace App\Filament\Resources\AffiliateLinks\Widgets;

use App\Models\AffiliateLinkClick;
use App\Models\Content;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AffiliateLinkUsageWidget extends TableWidget
{
    public ?Model $record = null;

    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return __('resource.affiliate_link_resource.usage_widget.heading');
    }

    protected function getTableDescription(): ?string
    {
        $linkId = $this->record?->id;
        if ($linkId === null) {
            return null;
        }

        $assigned = (int) AffiliateLinkClick::where('affiliate_link_id', $linkId)->sum('clicks');
        $total = (int) ($this->record->clicks ?? 0);
        $unassigned = max(0, $total - $assigned);

        return __('resource.affiliate_link_resource.usage_widget.unassigned', ['count' => $unassigned]);
    }

    protected function getTableQuery(): Builder
    {
        $linkId = $this->record?->id ?? 0;

        return Content::query()
            ->with('category')
            ->where('content', 'like', '%"affiliateLinkId":'.$linkId.'%')
            ->select('id', 'title', 'type', 'status', 'category_id', 'published_at', 'revised_at')
            ->addSelect([
                'link_clicks' => AffiliateLinkClick::query()
                    ->select('clicks')
                    ->whereColumn('content_id', 'contents.id')
                    ->where('affiliate_link_id', $linkId)
                    ->limit(1),
                'link_last_clicked_at' => AffiliateLinkClick::query()
                    ->select('last_clicked_at')
                    ->whereColumn('content_id', 'contents.id')
                    ->where('affiliate_link_id', $linkId)
                    ->limit(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->heading($this->getTableHeading())
            ->description($this->getTableDescription())
            ->columns([
                TextColumn::make('title')
                    ->label(__('resource.content_resource.form.title'))
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('resource.content_resource.form.type'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('resource.content_resource.form.status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('resource.content_resource.form.category'))
                    ->badge()
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('link_clicks')
                    ->label(__('resource.affiliate_link_resource.usage_widget.clicks'))
                    ->formatStateUsing(fn ($state) => (int) ($state ?? 0))
                    ->sortable(),
                TextColumn::make('link_last_clicked_at')
                    ->label(__('resource.affiliate_link_resource.usage_widget.last_clicked_at'))
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->isoFormat('L LT') : null)
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label(__('resource.content_resource.form.published_at'))
                    ->formatStateUsing(fn ($state) => $state?->isoFormat('L'))
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('revised_at')
                    ->label(__('resource.content_resource.form.revised_at'))
                    ->formatStateUsing(fn ($state) => $state?->isoFormat('L'))
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
