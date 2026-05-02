<?php

declare(strict_types=1);

namespace App\Validation;

use Attribute;
use Tempest\Database\PrimaryKey;
use Tempest\Validation\HasErrorMessage;
use Tempest\Validation\Rule;

use function Tempest\Database\query;

#[Attribute]
final readonly class UniqueEmailExcluding implements Rule, HasErrorMessage
{
    public function __construct(
        private PrimaryKey $excludeId,
    ) {}

    public function isValid(mixed $value): bool
    {
        return query('users')
            ->count()
            ->whereField('email', $value)
            ->whereNot('id', (string) $this->excludeId)
            ->execute() === 0;
    }

    public function getErrorMessage(): string
    {
        return 'This email is already in use.';
    }
}
