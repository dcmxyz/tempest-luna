<?php

declare(strict_types=1);

namespace Tests;

use App\Controllers\WelcomeController;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\MakesInertiaRequests;

use function Tempest\Reflection\reflect;
use function Tempest\Router\uri;

/**
 * @internal
 */
final class WelcomeControllerTest extends IntegrationTestCase
{
    use MakesInertiaRequests;

    #[Test]
    public function welcome_page_is_reachable(): void
    {
        $this
            ->inertia(uri(WelcomeController::class))
            ->assertOk()
            ->assertSee('Luna');
    }

    #[Test]
    public function packages_composer_version_can_be_retrieved(): void
    {
        $this
            ->inertia(uri(WelcomeController::class))
            ->assertOk()
            ->assertProp('tempestVersion')
            ->assertProp('inertiaTempestVersion')
            ->assertProp('phpVersion', PHP_VERSION);
    }

    #[Test]
    public function unknown_package_returns_unknown(): void
    {
        $reflectedGetPackageVersion = reflect(WelcomeController::class)
            ->getMethod('getPackageVersion')
            ->invokeArgs(
                object: new WelcomeController(),
                args: ['package/does-not-exist'],
            );

        $this->assertSame('Unknown', $reflectedGetPackageVersion);
    }
}
