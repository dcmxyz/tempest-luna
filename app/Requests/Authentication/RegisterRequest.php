<?php

namespace App\Requests\Authentication;

use App\Models\User;
use App\Validation\Unique;
use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\HasLength;
use Tempest\Validation\Rules\IsEmail;
use Tempest\Validation\Rules\IsPassword;
use Tempest\Validation\TranslationKey;

final class RegisterRequest implements Request
{
    use IsRequest;

    #[TranslationKey('register_name')]
    #[HasLength(min: 2, max: 255)]
    public string $name;

    #[TranslationKey('register_email')]
    #[IsEmail, Unique(User::class, 'email')]
    public string $email;

    #[TranslationKey('register_password')]
    #[IsPassword(min: 12, mixedCase: true, numbers: true)]
    public string $password;
}
