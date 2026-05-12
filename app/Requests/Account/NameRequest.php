<?php

namespace App\Requests\Account;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\HasLength;

final class NameRequest implements Request
{
    use IsRequest;

    #[HasLength(min: 2, max: 255)]
    public string $name;
}
