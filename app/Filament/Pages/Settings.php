<?php

namespace App\Filament\Pages;

use App\Enums\ContentType;
use App\Filament\Forms\MetaDataFields;
use App\Models\Category;
use App\Models\Content;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Setting;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Settings extends Page
{
    use HasPageShield;

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function getTitle(): string|Htmlable
    {
        return __('settings.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('settings.navigation_label');
    }

    public function mount(): void
    {
        $this->form->fill([
            'site_name' => Setting::get('site.name'),
            'site_logo' => Setting::get('site.logo'),
            'site_logo_dark' => Setting::get('site.logo_dark'),
            'site_favicon' => Setting::get('site.favicon'),
            'menu_header' => Setting::get('menu.header'),
            'menu_footer' => Setting::get('menu.footer'),
            'menu_max_depth' => Setting::get('menu.max_depth'),
            'footer_slogan' => Setting::get('footer.slogan'),
            'social_instagram' => Setting::get('social.instagram'),
            'social_youtube' => Setting::get('social.youtube'),
            'social_pinterest' => Setting::get('social.pinterest'),
            'theme_light_primary' => Setting::get('theme.light_primary'),
            'theme_dark_primary' => Setting::get('theme.dark_primary'),
            'theme_light_secondary' => Setting::get('theme.light_secondary'),
            'theme_dark_secondary' => Setting::get('theme.dark_secondary'),
            'theme_light_background' => Setting::get('theme.light_background'),
            'theme_dark_background' => Setting::get('theme.dark_background'),
            'theme_light_text' => Setting::get('theme.light_text'),
            'theme_dark_text' => Setting::get('theme.dark_text'),
            'theme_light_surface' => Setting::get('theme.light_surface'),
            'theme_dark_surface' => Setting::get('theme.dark_surface'),
            'theme_light_text_muted' => Setting::get('theme.light_text_muted'),
            'theme_dark_text_muted' => Setting::get('theme.dark_text_muted'),
            'theme_light_text_subtle' => Setting::get('theme.light_text_subtle'),
            'theme_dark_text_subtle' => Setting::get('theme.dark_text_subtle'),
            'theme_light_border' => Setting::get('theme.light_border'),
            'theme_dark_border' => Setting::get('theme.dark_border'),
            'category_per_page' => Setting::get('content.category_page_size') ?? 12,
            'maintenance_pages' => json_decode(Setting::get('maintenance.pages') ?? '[]', true),
            'maintenance_enabled' => (bool) Setting::get('maintenance.enabled'),
            'maintenance_allowed_ips' => Setting::get('maintenance.allowed_ips'),
            'rss_default_image' => Setting::get('rss.default_image'),
            'rss_title' => Setting::get('rss.title'),
            'rss_description' => Setting::get('rss.description'),
            'site_privacy_page' => Setting::get('site.privacy_page'),
            'site_homepage_content' => Setting::get('site.homepage_content'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model(Setting::firstOrCreate(['group' => 'seo', 'key' => 'meta']))
            ->components([
                Tabs::make('Settings')
                    ->tabs([
                        Tab::make(__('settings.tabs.general'))
                            ->icon('heroicon-m-globe-alt')
                            ->schema([
                                Section::make(__('settings.tabs.general'))
                                    ->schema([
                                        TextInput::make('site_name')
                                            ->label(__('settings.fields.site_name'))
                                            ->required()
                                            ->maxLength(255),
                                        CuratorPicker::make('site_logo')
                                            ->label(__('settings.fields.site_logo'))
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml']),
                                        CuratorPicker::make('site_logo_dark')
                                            ->label(__('settings.fields.site_logo_dark'))
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml']),
                                        CuratorPicker::make('site_favicon')
                                            ->label(__('settings.fields.site_favicon'))
                                            ->acceptedFileTypes(['image/x-icon', 'image/vnd.microsoft.icon', 'image/png', 'image/svg+xml']),
                                        Select::make('site_homepage_content')
                                            ->label(__('settings.fields.site_homepage_content'))
                                            ->helperText(__('settings.fields.site_homepage_content_hint'))
                                            ->options(fn () => Content::where('type', ContentType::HOMEPAGE)
                                                ->pluck('title', 'id')
                                                ->toArray()
                                            )
                                            ->placeholder(__('settings.fields.site_homepage_content_default'))
                                            ->nullable()
                                            ->searchable()
                                            ->native(false),
                                        Select::make('site_privacy_page')
                                            ->label(__('settings.fields.site_privacy_page'))
                                            ->helperText(__('settings.fields.site_privacy_page_hint'))
                                            ->options(fn () => Content::whereIn(
                                                'id',
                                                MenuItem::where('model_type', Content::class)->pluck('model_id')
                                            )
                                                ->whereHas('path')
                                                ->pluck('title', 'id')
                                                ->toArray()
                                            )
                                            ->nullable()
                                            ->searchable()
                                            ->native(false),
                                    ]),
                            ]),
                        Tab::make(__('settings.tabs.menu'))
                            ->icon('heroicon-m-bars-3')
                            ->schema([
                                Section::make(__('settings.sections.header'))
                                    ->schema([
                                        Select::make('menu_header')
                                            ->label(__('settings.fields.menu_header'))
                                            ->options(Menu::pluck('name', 'id'))
                                            ->nullable()
                                            ->native(false),
                                        Select::make('menu_max_depth')
                                            ->label(__('settings.fields.menu_max_depth'))
                                            ->options([1 => '1', 2 => '2', 3 => '3'])
                                            ->default(3)
                                            ->native(false),
                                    ]),
                                Section::make(__('settings.sections.footer'))
                                    ->schema([
                                        Select::make('menu_footer')
                                            ->label(__('settings.fields.menu_footer'))
                                            ->options(Menu::pluck('name', 'id'))
                                            ->nullable()
                                            ->native(false),
                                        TextInput::make('footer_slogan')
                                            ->label(__('settings.fields.footer_slogan'))
                                            ->maxLength(255),
                                    ]),
                            ]),
                        Tab::make(__('settings.tabs.seo'))
                            ->icon('heroicon-m-magnifying-glass')
                            ->schema([
                                MetaDataFields::make(),
                            ]),
                        Tab::make(__('settings.tabs.social'))
                            ->icon('heroicon-m-share')
                            ->schema([
                                Section::make(__('settings.tabs.social'))
                                    ->schema([
                                        TextInput::make('social_instagram')
                                            ->label(__('settings.fields.social_instagram'))
                                            ->url()
                                            ->placeholder('https://www.instagram.com/...'),
                                        TextInput::make('social_youtube')
                                            ->label(__('settings.fields.social_youtube'))
                                            ->url()
                                            ->placeholder('https://www.youtube.com/...'),
                                        TextInput::make('social_pinterest')
                                            ->label(__('settings.fields.social_pinterest'))
                                            ->url()
                                            ->placeholder('https://www.pinterest.com/...'),
                                    ]),
                            ]),
                        Tab::make(__('settings.tabs.content'))
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Section::make(__('settings.sections.category'))
                                    ->schema([
                                        TextInput::make('category_per_page')
                                            ->label(__('settings.fields.category_per_page'))
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(100)
                                            ->default(12)
                                            ->required(),
                                    ]),
                            ]),
                        Tab::make(__('settings.tabs.maintenance'))
                            ->icon('heroicon-m-shield-exclamation')
                            ->schema([
                                Section::make(__('settings.tabs.maintenance'))
                                    ->schema([
                                        Select::make('maintenance_pages')
                                            ->label(__('settings.fields.maintenance_pages'))
                                            ->hint(__('settings.fields.maintenance_pages_hint'))
                                            ->multiple()
                                            ->searchable()
                                            ->options(
                                                Content::published()
                                                    ->whereHas('path', fn ($q) => $q->whereNotNull('url'))
                                                    ->with('path')
                                                    ->get()
                                                    ->mapWithKeys(fn ($c) => [$c->id => $c->title.' ('.$c->path->url.')'])
                                            )
                                            ->native(false),
                                        Toggle::make('maintenance_enabled')
                                            ->label(__('settings.fields.maintenance_enabled'))
                                            ->live(),
                                        Placeholder::make('maintenance_warning')
                                            ->label('')
                                            ->content(new HtmlString(
                                                '<p class="text-sm font-medium text-danger-600 dark:text-danger-400">⚠ '.__('settings.fields.maintenance_warning').'</p>'
                                            ))
                                            ->visible(fn ($get) => (bool) $get('maintenance_enabled')),
                                        Textarea::make('maintenance_allowed_ips')
                                            ->label(__('settings.fields.maintenance_allowed_ips'))
                                            ->hint(__('settings.fields.maintenance_allowed_ips_hint'))
                                            ->hintActions([
                                                Action::make('add_my_ip')
                                                    ->label(__('settings.actions.add_my_ip'))
                                                    ->icon('heroicon-m-plus-circle')
                                                    ->action(function ($get, $set) {
                                                        $myIp = request()->ip();
                                                        $existing = array_filter(array_map('trim', explode("\n", $get('maintenance_allowed_ips') ?? '')));
                                                        if (! in_array($myIp, $existing)) {
                                                            $existing[] = $myIp;
                                                        }
                                                        $set('maintenance_allowed_ips', implode("\n", $existing));
                                                    }),
                                            ])
                                            ->rows(5),
                                    ]),
                            ]),
                        Tab::make(__('settings.tabs.rss'))
                            ->icon('heroicon-m-rss')
                            ->schema([
                                Section::make(__('settings.tabs.rss'))
                                    ->schema([
                                        TextInput::make('rss_title')
                                            ->label(__('settings.fields.rss_title'))
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->helperText(fn ($state) => strlen($state ?? '').' / 255 '.__('resource.default_form_fields.signs')),
                                        TextInput::make('rss_description')
                                            ->label(__('settings.fields.rss_description'))
                                            ->maxLength(500)
                                            ->live(onBlur: true)
                                            ->helperText(fn ($state) => strlen($state ?? '').' / 500 '.__('resource.default_form_fields.signs')),
                                        CuratorPicker::make('rss_default_image')
                                            ->label(__('settings.fields.rss_default_image'))
                                            ->hint(__('settings.fields.rss_default_image_hint'))
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                                    ]),
                                Section::make(__('settings.sections.rss_feeds'))
                                    ->schema(array_merge(
                                        [
                                            Placeholder::make('rss_url_main')
                                                ->label(__('settings.sections.rss_main'))
                                                ->content(new HtmlString('<code style="user-select:all">'.url('/rss').'</code>')),
                                        ],
                                        Category::with('path')->get()
                                            ->map(fn ($cat) => Placeholder::make('rss_url_cat_'.$cat->id)
                                                ->label($cat->name)
                                                ->content(new HtmlString('<code style="user-select:all">'.route('feed.category', $cat->path?->slug).'</code>'))
                                            )->all()
                                    )),
                            ]),
                        Tab::make(__('settings.tabs.theme'))
                            ->icon('heroicon-m-swatch')
                            ->schema([
                                Section::make(__('settings.tabs.theme'))
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                ColorPicker::make('theme_light_primary')
                                                    ->label(__('settings.fields.theme_light_primary')),
                                                ColorPicker::make('theme_dark_primary')
                                                    ->label(__('settings.fields.theme_dark_primary')),
                                                ColorPicker::make('theme_light_secondary')
                                                    ->label(__('settings.fields.theme_light_secondary')),
                                                ColorPicker::make('theme_dark_secondary')
                                                    ->label(__('settings.fields.theme_dark_secondary')),
                                                ColorPicker::make('theme_light_background')
                                                    ->label(__('settings.fields.theme_light_background')),
                                                ColorPicker::make('theme_dark_background')
                                                    ->label(__('settings.fields.theme_dark_background')),
                                                ColorPicker::make('theme_light_text')
                                                    ->label(__('settings.fields.theme_light_text')),
                                                ColorPicker::make('theme_dark_text')
                                                    ->label(__('settings.fields.theme_dark_text')),
                                                ColorPicker::make('theme_light_surface')
                                                    ->label(__('settings.fields.theme_light_surface')),
                                                ColorPicker::make('theme_dark_surface')
                                                    ->label(__('settings.fields.theme_dark_surface')),
                                                ColorPicker::make('theme_light_text_muted')
                                                    ->label(__('settings.fields.theme_light_text_muted')),
                                                ColorPicker::make('theme_dark_text_muted')
                                                    ->label(__('settings.fields.theme_dark_text_muted')),
                                                ColorPicker::make('theme_light_text_subtle')
                                                    ->label(__('settings.fields.theme_light_text_subtle')),
                                                ColorPicker::make('theme_dark_text_subtle')
                                                    ->label(__('settings.fields.theme_dark_text_subtle')),
                                                ColorPicker::make('theme_light_border')
                                                    ->label(__('settings.fields.theme_light_border')),
                                                ColorPicker::make('theme_dark_border')
                                                    ->label(__('settings.fields.theme_dark_border')),
                                            ]),
                                    ]),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make($this->getFormActions()),
                    ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('settings.actions.save'))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = [
            'site_name' => ['group' => 'site',   'key' => 'name',      'type' => 'string'],
            'site_logo' => ['group' => 'site',   'key' => 'logo',      'type' => 'integer'],
            'site_logo_dark' => ['group' => 'site',   'key' => 'logo_dark', 'type' => 'integer'],
            'site_favicon' => ['group' => 'site',   'key' => 'favicon',   'type' => 'integer'],
            'menu_header' => ['group' => 'menu',   'key' => 'header',    'type' => 'integer'],
            'menu_footer' => ['group' => 'menu',   'key' => 'footer',    'type' => 'integer'],
            'menu_max_depth' => ['group' => 'menu',   'key' => 'max_depth', 'type' => 'integer'],
            'footer_slogan' => ['group' => 'footer', 'key' => 'slogan',    'type' => 'string'],
            'social_instagram' => ['group' => 'social', 'key' => 'instagram', 'type' => 'string'],
            'social_youtube' => ['group' => 'social', 'key' => 'youtube',   'type' => 'string'],
            'social_pinterest' => ['group' => 'social', 'key' => 'pinterest', 'type' => 'string'],
            'theme_light_primary' => ['group' => 'theme', 'key' => 'light_primary',     'type' => 'string'],
            'theme_dark_primary' => ['group' => 'theme', 'key' => 'dark_primary',      'type' => 'string'],
            'theme_light_secondary' => ['group' => 'theme', 'key' => 'light_secondary',   'type' => 'string'],
            'theme_dark_secondary' => ['group' => 'theme', 'key' => 'dark_secondary',    'type' => 'string'],
            'theme_light_background' => ['group' => 'theme', 'key' => 'light_background',  'type' => 'string'],
            'theme_dark_background' => ['group' => 'theme', 'key' => 'dark_background',   'type' => 'string'],
            'theme_light_text' => ['group' => 'theme', 'key' => 'light_text',        'type' => 'string'],
            'theme_dark_text' => ['group' => 'theme', 'key' => 'dark_text',         'type' => 'string'],
            'theme_light_surface' => ['group' => 'theme', 'key' => 'light_surface',     'type' => 'string'],
            'theme_dark_surface' => ['group' => 'theme', 'key' => 'dark_surface',      'type' => 'string'],
            'theme_light_text_muted' => ['group' => 'theme', 'key' => 'light_text_muted',  'type' => 'string'],
            'theme_dark_text_muted' => ['group' => 'theme', 'key' => 'dark_text_muted',   'type' => 'string'],
            'theme_light_text_subtle' => ['group' => 'theme', 'key' => 'light_text_subtle', 'type' => 'string'],
            'theme_dark_text_subtle' => ['group' => 'theme', 'key' => 'dark_text_subtle',  'type' => 'string'],
            'theme_light_border' => ['group' => 'theme',    'key' => 'light_border', 'type' => 'string'],
            'theme_dark_border' => ['group' => 'theme',    'key' => 'dark_border',  'type' => 'string'],
            'category_per_page' => ['group' => 'content',     'key' => 'category_page_size', 'type' => 'integer'],
            'maintenance_enabled' => ['group' => 'maintenance', 'key' => 'enabled',            'type' => 'boolean'],
            'maintenance_allowed_ips' => ['group' => 'maintenance', 'key' => 'allowed_ips',        'type' => 'string'],
            'rss_default_image' => ['group' => 'rss',  'key' => 'default_image', 'type' => 'integer'],
            'rss_title' => ['group' => 'rss',  'key' => 'title',         'type' => 'string'],
            'rss_description' => ['group' => 'rss',  'key' => 'description',   'type' => 'string'],
            'site_privacy_page' => ['group' => 'site', 'key' => 'privacy_page',     'type' => 'integer'],
            'site_homepage_content' => ['group' => 'site', 'key' => 'homepage_content', 'type' => 'integer'],
        ];

        foreach ($settings as $field => $config) {
            Setting::updateOrCreate(
                ['group' => $config['group'], 'key' => $config['key']],
                ['value' => $data[$field], 'type' => $config['type']]
            );
        }

        Setting::updateOrCreate(
            ['group' => 'maintenance', 'key' => 'pages'],
            ['value' => json_encode($data['maintenance_pages'] ?? []), 'type' => 'string']
        );

        $this->form->saveRelationships();

        Notification::make()
            ->title(__('settings.notifications.saved'))
            ->success()
            ->send();
    }

    public static function getNavigationGroup(): string|null|\UnitEnum
    {
        return __('resource.navigation_groups.system');
    }
}
