<?php

namespace App\Requests\Account;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\HasLength;
use Tempest\Validation\TranslationKey;

final class NameRequest implements Request
{
    use IsRequest;

    #[TranslationKey('name')]
    #[HasLength(min: 2, max: 255)]
    public string $name;
}
