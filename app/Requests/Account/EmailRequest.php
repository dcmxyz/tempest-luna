<?php

namespace App\Requests\Account;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\IsEmail;

final class EmailRequest implements Request
{
    use IsRequest;

    #[IsEmail]
    public string $email;
}
