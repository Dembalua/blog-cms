<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getLastNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getPasswordRulesFormComponent(),
            ]);
    }

    public function getLastNameFormComponent()
    {
        return TextInput::make('lastname')
            ->label(__('resource.user_resource.form.last_name'))
            ->required()
            ->maxLength(255);
    }

    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->rule(Password::min(8)->max(255)->letters()->mixedCase()->numbers()->symbols())
            ->live();
    }

    protected function getPasswordRulesFormComponent(): Component
    {
        return ViewField::make('password_rules')
            ->view('filament.components.password-rules')
            ->hiddenLabel()
            ->visible(fn (Get $get): bool => filled($get('password')));
    }
}
