<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resource.user_resource.form.role'))
                    ->description(__('resource.user_resource.form.role_description'))
                    ->schema([
                        Select::make('roles')
                            ->label(__('resource.user_resource.form.roles'))
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                    ])->columnSpan(2),
                Section::make(__('resource.user_resource.form.personal_information'))
                    ->description(__('resource.user_resource.form.personal_information_description'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('resource.default_form_fields.name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('lastname')
                            ->label(__('resource.user_resource.form.last_name'))
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label(__('resource.user_resource.form.email'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                    ])
                    ->columnSpan(1),
                Section::make(__('resource.user_resource.form.password'))
                    ->description(__('resource.user_resource.form.password_description'))
                    ->schema([
                        TextInput::make('password')
                            ->label(__('resource.user_resource.form.password'))
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->rule(Password::min(8)
                                ->max(255)
                                ->letters()
                                ->mixedCase()
                                ->numbers()
                                ->symbols()
                            )
                            ->live(),

                        TextInput::make('passwordConfirmation')
                            ->label(__('resource.user_resource.form.password_confirmation'))
                            ->password()
                            ->revealable()
                            ->required(fn (Get $get): bool => filled($get('password')))
                            ->same('password')
                            ->dehydrated(false)
                            ->live(),

                        ViewField::make('password_rules')
                            ->view('filament.components.password-rules')
                    ])->columnSpan(1),
            ]);
    }
}
