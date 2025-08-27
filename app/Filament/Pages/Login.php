<?php

namespace App\Filament\Pages;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\LoginResponse;
use Filament\Pages\Page;
use Illuminate\Validation\ValidationException;

class Login extends \Filament\Pages\Auth\Login
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\TextInput::make('nip')
                    ->required(),
                $this->getPasswordFormComponent()        
        ]);
    }

    // public function authenticate(): ?LoginResponse
    // {
    //     try {
    //         $this->rateLimit(5);
    //     } catch (TooManyRequestsException $exception) {
    //         $this->getRateLimitedNotification($exception)?->send();

    //         return null;
    //     }

    //     $data = $this->form->getState();

    //     if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
    //         $this->throwFailureValidationException();
    //     }

    //     $user = Filament::auth()->user();

    //     if (
    //         ($user instanceof FilamentUser) &&
    //         (! $user->canAccessPanel(Filament::getCurrentPanel()))
    //     ) {
    //         Filament::auth()->logout();

    //         $this->throwFailureValidationException();
    //     }

    //     session()->regenerate();

    //     return app(LoginResponse::class);
    // }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.nip' => 'Data nip ini tidak terdaftar',
        ]);
    }

    public function getCredentialsFromFormData(array $data): array
    {
        return [
            'nip' => $data['nip'],
            'password' => $data['password'],
        ];
    }
}
