<?php

namespace App\Validation;

use Attribute;
use Tempest\Validation\Rule;

use function Tempest\Database\query;

#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Unique implements Rule
{
    public function __construct(
        private string $table,
        private string $column,
    ) {}

    public function isValid(mixed $value): bool
    {
        return query($this->table)
            ->count()
            ->whereField($this->column, $value)
            ->execute() === 0;
    }
}
