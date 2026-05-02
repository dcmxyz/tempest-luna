<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Validation\FailWithMessage;
use Tempest\Validation\Exceptions\ValidationFailed;
use Tempest\Validation\FailingRule;
use Tempest\Validation\Validator;

use function Tempest\Container\get;

/**
 * @throws ValidationFailed
 */
function fail_validation(string $field, string $message, mixed $value = null): never
{
    throw get(Validator::class)->createValidationFailureException([
        $field => [new FailingRule(new FailWithMessage($message), value: $value)],
    ]);
}