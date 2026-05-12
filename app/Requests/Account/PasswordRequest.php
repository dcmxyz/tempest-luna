<?php

namespace App\Requests\Account;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\IsNotEmptyString;
use Tempest\Validation\Rules\IsPassword;

final class PasswordRequest implements Request
{
    use IsRequest;

    #[IsNotEmptyString]
    public string $current_password;

    #[IsPassword(min: 12, mixedCase: true, numbers: true)]
    public string $password;
}
