<?php

declare(strict_types=1);

namespace App\Requests\Authentication;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\IsNotEmptyString;
use Tempest\Validation\Rules\IsPassword;

final class ResetPasswordRequest implements Request
{
    use IsRequest;

    #[IsNotEmptyString]
    public string $token;

    #[IsPassword(min: 12, mixedCase: true, numbers: true)]
    public string $password;
}
