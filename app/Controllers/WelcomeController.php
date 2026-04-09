<?php

declare(strict_types=1);

namespace App\Controllers;

use Inertia\Response;
use Tempest\Router\Get;

use function Tempest\root_path;
use function Tempest\Support\arr;

final class WelcomeController
{
    #[Get(uri: '/')]
    public function __invoke(): Response
    {
        return inertia(
            component: 'Welcome',
            props: [
                'tempestVersion' => $this->getPackageVersion(wantedPackage: 'tempest/framework'),
                'inertiaTempestVersion' => $this->getPackageVersion(wantedPackage: 'maartendekker/inertia-tempest'),
                'phpVersion' => PHP_VERSION,
            ],
        );
    }

    private function getPackageVersion(string $wantedPackage): string
    {
        $lockFile = root_path('composer.lock');

        if (! file_exists($lockFile)) {
            return '';
        }

        try {
            $lock = json_decode(
                json: file_get_contents($lockFile),
                associative: true,
                depth: 512,
                flags: JSON_THROW_ON_ERROR,
            );

            $wantedPackageInfos = arr($lock['packages'])
                ->filter(static function (array $package) use ($wantedPackage): bool {
                    return $package['name'] === $wantedPackage;
                })->first();

            if ($wantedPackageInfos !== null) {
                return ltrim($wantedPackageInfos['version'], 'v');
            }
        } catch (\Throwable $exception) {
            // Do nothing ...
        }

        return 'Unknown';
    }
}
