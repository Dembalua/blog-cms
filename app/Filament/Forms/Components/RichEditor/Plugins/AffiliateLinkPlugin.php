<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components\RichEditor\Plugins;

use App\Filament\Forms\Components\RichEditor\TipTapExtensions\AffiliateLinkExtension;
use App\Models\AffiliateLink;
use App\Models\AffiliateNetwork;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentAsset;

class AffiliateLinkPlugin implements RichContentPlugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getTipTapPhpExtensions(): array
    {
        return [
            app(AffiliateLinkExtension::class),
        ];
    }

    public function getTipTapJsExtensions(): array
    {
        return [
            FilamentAsset::getScriptSrc('affiliate-link-extension'),
        ];
    }

    public function getEditorTools(): array
    {
        return [
            RichEditorTool::make('affiliateLink')
                ->label(__('rich-editor-blocks.affiliate_link.label'))
                ->icon('heroicon-m-link')
                ->action('insertAffiliateLink')
                ->activeKey('affiliateLink')
                ->extraAttributes(['class' => 'fi-affiliate-link-tool']),
        ];
    }

    public function getEditorActions(): array
    {
        return [
            Action::make('insertAffiliateLink')
                ->modalWidth(Width::Large)
                ->modalHeading(__('rich-editor-blocks.affiliate_link.modal_heading'))
                ->schema([
                    Select::make('affiliate_network_id')
                        ->label(__('rich-editor-blocks.affiliate_link.fields.network'))
                        ->options(AffiliateNetwork::orderBy('name')->pluck('name', 'id'))
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('affiliate_link_id', null)),
                    Select::make('affiliate_link_id')
                        ->label(__('rich-editor-blocks.affiliate_link.fields.link'))
                        ->options(fn (Get $get) => AffiliateLink::where('is_active', true)
                            ->when($get('affiliate_network_id'), fn ($q, $id) => $q->where('affiliate_network_id', $id))
                            ->orderBy('name')
                            ->pluck('name', 'id')
                        )
                        ->required()
                        ->searchable()
                        ->native(false),
                ])
                ->action(function (array $arguments, array $data, RichEditor $component): void {
                    $editorSelection = $arguments['editorSelection'] ?? null;
                    $isSinglePoint = ($editorSelection['anchor'] ?? null) === ($editorSelection['head'] ?? null);

                    if ($isSinglePoint) {
                        $link = AffiliateLink::find($data['affiliate_link_id']);
                        $commands = [
                            EditorCommand::make('insertContent', [[
                                'type' => 'text',
                                'text' => $link?->name ?? '',
                                'marks' => [[
                                    'type' => 'affiliateLink',
                                    'attrs' => ['affiliateLinkId' => (int) $data['affiliate_link_id']],
                                ]],
                            ]]),
                        ];
                    } else {
                        $commands = [
                            EditorCommand::make('setAffiliateLink', [['affiliateLinkId' => (int) $data['affiliate_link_id']]]),
                        ];
                    }

                    $component->runCommands($commands, editorSelection: $editorSelection);
                }),
        ];
    }
}
