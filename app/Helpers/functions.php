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

function parse_user_agent(string $userAgent): string
{
    $browser = 'Unknown Browser';
    $os = 'Unknown OS';
    $device = 'Desktop';

    $browsers = [
        'Edg'            => 'Edge',
        'OPR'            => 'Opera',
        'Opera'          => 'Opera',
        'SamsungBrowser' => 'Samsung Browser',
        'Chrome'         => 'Chrome',
        'Firefox'        => 'Firefox',
        'Safari'         => 'Safari',
        'MSIE'           => 'Internet Explorer',
        'Trident'        => 'Internet Explorer',
    ];

    foreach ($browsers as $token => $name) {
        if (str_contains($userAgent, $token)) {
            $browser = $name;
            break;
        }
    }

    $operatingSystems = [
        'Macintosh' => 'macOS',
        'iPhone'    => 'iOS',
        'iPad'      => 'iPadOS',
        'Android'   => 'Android',
        'Linux'     => 'Linux',
        'Windows'   => 'Windows',
    ];

    foreach ($operatingSystems as $token => $name) {
        if (str_contains($userAgent, $token)) {
            $os = $name;
            break;
        }
    }

    if (str_contains($userAgent, 'Mobi') || (str_contains($userAgent, 'Android') && !str_contains($userAgent, 'Tablet'))) {
        $device = 'Mobile';
    } elseif (str_contains($userAgent, 'iPad') || str_contains($userAgent, 'Tablet')) {
        $device = 'Tablet';
    }

    return "$browser - $device ($os)";
}
