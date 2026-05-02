<?php

declare(strict_types=1);

namespace App\Validation;

use Attribute;
use Tempest\Validation\HasErrorMessage;
use Tempest\Validation\Rule;

#[Attribute]
final readonly class FailWithMessage implements Rule, HasErrorMessage
{
    public function __construct(
        private string $message
    ) {}

    public function isValid(mixed $value): bool
    {
        return false;
    }

    public function getErrorMessage(): string
    {
        return $this->message;
    }
}