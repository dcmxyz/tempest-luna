<?php

namespace App\Requests\Authentication;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\IsEmail;
use Tempest\Validation\Rules\IsNotEmptyString;

final class LoginRequest implements Request
{
    use IsRequest;

    #[IsNotEmptyString, IsEmail]
    public string $email;

    #[IsNotEmptyString]
    public string $password;
}
