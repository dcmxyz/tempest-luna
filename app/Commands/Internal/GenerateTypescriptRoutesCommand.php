<?php

declare(strict_types=1);

namespace App\Commands\Internal;

use Tempest\Console\Console;
use Tempest\Console\ConsoleCommand;
use Tempest\Router\RouteConfig;

use function Tempest\internal_storage_path;

final readonly class GenerateTypescriptRoutesCommand
{
    public function __construct(
        private Console $console,
        private RouteConfig $routeConfig,
    ) {}

    #[ConsoleCommand(
        name: 'internal:generate-typescript-routes',
        description: 'Generate the typescript routes file used by the uri() helper function in the frontend.',
    )]
    public function __invoke(): void
    {
        $routes = $this->collectRoutes();

        $json = json_encode($routes, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
        $output = "// This file is auto-generated. Do not edit manually.\nexport const routes = {$json} as const;\n";

        $path = internal_storage_path('typescript/routes.ts');
        $directory = dirname($path);

        if (! is_dir($directory) && ! mkdir($directory, 0o755, recursive: true) && ! is_dir($directory)) {
            throw new \RuntimeException("Failed to create directory: {$directory}");
        }

        file_put_contents($path, $output);

        $this->console->success('Typescript routes generated');
    }

    private function collectRoutes(): array
    {
        $discovered = [];

        foreach ([$this->routeConfig->staticRoutes, $this->routeConfig->dynamicRoutes] as $group) {
            foreach ($group as $routesForMethod) {
                foreach ($routesForMethod as $route) {
                    $discovered["{$route->uri}:{$route->method->value}"] = [
                        'method' => $route->method->value,
                        'uri' => $route->uri,
                    ];
                }
            }
        }

        $routes = array_values($discovered);
        usort($routes, static fn ($a, $b) => strcmp($a['uri'], $b['uri']));

        return $routes;
    }
}
