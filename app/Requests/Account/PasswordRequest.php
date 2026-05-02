<?php

namespace App\Requests\Account;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\IsPassword;
use Tempest\Validation\TranslationKey;

final class PasswordRequest implements Request
{
    use IsRequest;

    #[TranslationKey('current_password')]
    public string $current_password;

    #[TranslationKey('password')]
    #[IsPassword(min: 12, mixedCase: true, numbers: true)]
    public string $password;
}
