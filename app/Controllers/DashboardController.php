<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\MustBeAuthenticated;
use Inertia\Response;
use Tempest\Router\Get;

final class DashboardController
{
    public function __construct() {}

    #[Get(uri: '/dashboard', middleware: [MustBeAuthenticated::class])]
    public function __invoke(): Response
    {
        return inertia(component: 'Dashboard');
    }
}
