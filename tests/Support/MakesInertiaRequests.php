<?php

declare(strict_types=1);

namespace Tests\Support;

use Inertia\Support\Header;
use Tempest\Framework\Testing\Http\TestResponseHelper;

use function Tempest\Support\Arr\merge;

/**
 * @internal
 */
trait MakesInertiaRequests
{
    private function inertia(
        string $uri,
        array $query = [],
        array $headers = [],
    ): InertiaTestResponse|TestResponseHelper {
        $response = $this->http->get(
            uri: $uri,
            query: $query,
            headers: merge($headers, [Header::INERTIA => 'true']),
        );

        return new InertiaTestResponse($response);
    }
}
