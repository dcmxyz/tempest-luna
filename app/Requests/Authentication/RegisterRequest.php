<?php

namespace App\Requests\Authentication;

use App\Models\User;
use App\Validation\Unique;
use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\HasLength;
use Tempest\Validation\Rules\IsEmail;
use Tempest\Validation\Rules\IsPassword;

final class RegisterRequest implements Request
{
    use IsRequest;

    #[HasLength(min: 2, max: 255)]
    public string $name;

    #[IsEmail, Unique(User::class, 'email')]
    public string $email;

    #[IsPassword(min: 12, mixedCase: true, numbers: true)]
    public string $password;
}
