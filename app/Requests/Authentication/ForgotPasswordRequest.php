<?php

namespace App\Requests\Authentication;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\IsEmail;
use Tempest\Validation\Rules\IsNotEmptyString;
use Tempest\Validation\TranslationKey;

final class ForgotPasswordRequest implements Request
{
    use IsRequest;

    #[TranslationKey('forgot_password_email')]
    #[IsNotEmptyString, IsEmail]
    public string $email;
}
