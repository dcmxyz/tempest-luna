<?php

declare(strict_types=1);

namespace Tests\Support;

use Tempest\Framework\Testing\Http\TestResponseHelper;

use function Tempest\Reflection\reflect;

/**
 * @internal
 *
 * This class helps with inertia tests.
 */
final class InertiaTestResponse
{
    private array $props;

    public function __construct(
        private readonly TestResponseHelper $response,
    ) {
        $inertiaResponse = reflect($response, 'response')->getValue($response);
        $this->props = reflect($inertiaResponse, 'props')->getValue($inertiaResponse) ?? [];
    }

    public function assertProp(string $key, mixed $expected = null): self
    {
        assert(array_key_exists($key, $this->props), "Prop [{$key}] not found.");

        if ($expected !== null) {
            assert($this->props[$key] === $expected, "Prop [{$key}] expected [{$expected}], got [{$this->props[$key]}].");
        }

        return $this;
    }

    public function __call(string $name, array $arguments): mixed
    {
        $result = $this->response->{$name}(...$arguments);

        return $result instanceof TestResponseHelper ? $this : $result;
    }
}
