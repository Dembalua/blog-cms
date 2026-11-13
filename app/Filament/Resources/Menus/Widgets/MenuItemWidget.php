<?php

namespace App\Filament\Resources\Menus\Widgets;

use App\Enums\ContentType;
use App\Models\Category;
use App\Models\Content;
use App\Models\MenuItem;
use App\Models\Setting;
use App\Services\PathService;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use SolutionForest\FilamentTree\Actions\CreateAction;
use SolutionForest\FilamentTree\Actions\DeleteAction;
use SolutionForest\FilamentTree\Actions\EditAction;
use SolutionForest\FilamentTree\Widgets\Tree;

class MenuItemWidget extends Tree
{
    protected static string $model = MenuItem::class;

    protected bool $enableTreeTitle = true;

    public ?Model $record = null;

    public static function getMaxDepth(): int
    {
        return (int) (Setting::get('menu.max_depth') ?? 3);
    }

    public function getTreeTitle(): string
    {
        return __('resource.menu_resource.widget.tree_title');
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make()
                ->columns(2)
                ->schema([
                    Select::make('parent_id')
                        ->label(__('resource.menu_resource.widget.parent'))
                        ->helperText(__('resource.menu_resource.widget.parent_helper'))
                        ->columnSpanFull()
                        ->options(function () {
                            $maxDepth = (int) (Setting::get('menu.max_depth') ?? 3);

                            return MenuItem::where('menu_id', $this->record?->id)
                                ->where('model_type', Category::class)
                                ->get()
                                ->filter(fn ($item) => $item->getDepth() < $maxDepth - 1)
                                ->pluck('title', 'id');
                        })
                        ->visible(fn () => MenuItem::where('menu_id', $this->record?->id)
                            ->where('model_type', Category::class)
                            ->exists()),
                    Group::make()
                        ->columns(2)
                        ->columnSpanFull()
                        ->schema([
                            Select::make('model_type')
                                ->label(__('resource.menu_resource.widget.model_type_label'))
                                ->options([
                                    'page' => __('resource.menu_resource.widget.mode_type_options.page'),
                                    'post' => __('resource.menu_resource.widget.mode_type_options.post'),
                                    'category' => __('resource.menu_resource.widget.mode_type_options.category'),
                                ])
                                ->required()
                                ->live(),
                            Select::make('model_id')
                                ->label(__('resource.menu_resource.widget.model_id_label'))
                                ->helperText(fn (Get $get) => in_array($get('model_type'), ['page', 'post'])
                                    ? __('resource.menu_resource.widget.model_id_hint')
                                    : null)
                                ->options(function (Get $get, ?Model $record) {
                                    $modelType = $get('model_type');

                                    $usedIds = MenuItem::when(
                                        $record,
                                        fn ($q) => $q->where('id', '!=', $record->id)
                                    )
                                        ->where('model_type', match ($modelType) {
                                            'page', 'post' => Content::class,
                                            'category' => Category::class,
                                            default => '',
                                        })
                                        ->pluck('model_id');

                                    return match ($modelType) {
                                        'page' => Content::where('type', ContentType::PAGE)
                                            ->whereNull('category_id')
                                            ->whereNotIn('id', $usedIds)
                                            ->pluck('title', 'id'),
                                        'post' => Content::where('type', ContentType::POST)
                                            ->whereNull('category_id')
                                            ->whereNotIn('id', $usedIds)
                                            ->pluck('title', 'id'),
                                        'category' => Category::whereNotIn('id', $usedIds)
                                            ->pluck('name', 'id'),
                                        default => [],
                                    };
                                })
                                ->searchable()
                                ->preload()
                                ->required()
                                ->visible(fn (Get $get) => filled($get('model_type')))
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                    if (! $state) {
                                        return;
                                    }

                                    $title = match ($get('model_type')) {
                                        'page', 'post' => Content::find($state)?->title,
                                        'category' => Category::find($state)?->name,
                                        default => null,
                                    };

                                    if ($title) {
                                        $set('title', $title);
                                    }
                                }),
                        ]),
                    Hidden::make('edit_title')
                        ->dehydrated(false),
                    TextInput::make('title')
                        ->label(__('resource.menu_resource.widget.title_label'))
                        ->columnSpanFull()
                        ->maxLength(80)
                        ->helperText(fn ($state) => strlen($state ?? '').' / 80 '.__('resource.default_form_fields.signs'))
                        ->live(onBlur: true)
                        ->hintIcon('heroicon-m-question-mark-circle')
                        ->hintIconTooltip(__('resource.menu_resource.widget.title_hint'))
                        ->required()
                        ->disabled(fn (Get $get) => ! $get('edit_title'))
                        ->dehydrated()
                        ->prefixAction(
                            Action::make('edit_title')
                                ->icon('heroicon-m-pencil')
                                ->tooltip(__('resource.menu_resource.widget.title_tooltip'))
                                ->action(fn (Set $set, Get $get) => $set('edit_title', ! $get('edit_title')))
                        ),
                ]),
        ];
    }

    protected function getTreeQuery(): Builder
    {
        return MenuItem::query()
            ->where('menu_id', $this->record?->id);
    }

    protected function getTreeToolbarActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data) {
                    if (in_array($data['model_type'], ['page', 'post'])) {
                        $data['model_type'] = Content::class;
                    } else {
                        $data['model_type'] = Category::class;
                    }
                    $data['menu_id'] = $this->record?->id;

                    return $data;
                })
                ->after(function (MenuItem $record) {
                    app(PathService::class)->generateForMenuItem($record);
                }),
        ];
    }

    protected function getTreeActions(): array
    {
        return [
            EditAction::make()
                ->after(function (MenuItem $record) {
                    app(PathService::class)->generateForMenuItem($record);
                }),
            DeleteAction::make()
                ->after(function (MenuItem $record) {
                    if ($record->model?->path) {
                        $record->model->path->update(['url' => null]);
                    }
                    foreach ($record->children as $child) {
                        app(PathService::class)->generateForMenuItem($child);
                    }
                }),
        ];
    }
}
