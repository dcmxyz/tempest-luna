<?php

namespace App\Requests\Authentication;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\IsEmail;
use Tempest\Validation\Rules\IsNotEmptyString;
use Tempest\Validation\TranslationKey;

final class LoginRequest implements Request
{
    use IsRequest;

    #[TranslationKey('login_email')]
    #[IsNotEmptyString, IsEmail]
    public string $email;

    #[TranslationKey('login_password')]
    #[IsNotEmptyString]
    public string $password;
}
