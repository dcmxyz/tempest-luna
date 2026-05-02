<?php

namespace App\Requests\Account;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\IsEmail;
use Tempest\Validation\TranslationKey;

final class EmailRequest implements Request
{
    use IsRequest;

    #[TranslationKey('email')]
    #[IsEmail]
    public string $email;
}
