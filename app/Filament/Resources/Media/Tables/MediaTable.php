<?php

declare(strict_types=1);

namespace App\Filament\Resources\Media\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\Tables\MediaTable as BaseMediaTable;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class MediaTable extends BaseMediaTable
{
    /** @throws Exception */
    public static function configure(Table $table): Table
    {
        return parent::configure($table)
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Media $record) {
                        $disk = Storage::disk($record->disk);
                        $tempPath = tempnam(sys_get_temp_dir(), 'download');
                        file_put_contents($tempPath, $disk->get($record->path));

                        return response()->download($tempPath, $record->name.'.'.$record->ext)
                            ->deleteFileAfterSend(true);
                    })
                    ->authorize('download'),
            ]);
    }

    /** @throws Exception */
    public static function getDefaultTableColumns(): array
    {
        return [
            CuratorColumn::make('url')
                ->label(trans('curator::tables.columns.url'))
                ->imageSize(40),
            TextColumn::make('title')
                ->label(trans(__('curator::forms.fields.title')))
                ->searchable()
                ->sortable(),
            TextColumn::make('ext')
                ->label(trans('curator::tables.columns.ext'))
                ->sortable(),
            TextColumn::make('size')
                ->label(trans('curator::tables.columns.size'))
                ->formatStateUsing(fn ($record): string => Curator::sizeForHumans($record->size))
                ->sortable(),
            TextColumn::make('dimensions')
                ->label(trans('curator::tables.columns.dimensions'))
                ->getStateUsing(fn ($record): ?string => $record->width ? $record->width.'x'.$record->height : null)
                ->sortable(),
            TextColumn::make('created_at')
                ->label(trans('curator::tables.columns.created_at'))
                ->date('Y-m-d')
                ->sortable(),
        ];
    }
}
