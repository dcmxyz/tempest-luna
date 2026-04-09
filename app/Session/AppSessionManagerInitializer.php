<?php

declare(strict_types=1);

namespace App\Session;

use Tempest\Container\Container;
use Tempest\Container\Initializer;
use Tempest\Container\Singleton;
use Tempest\Http\Session\SessionManager;

final class AppSessionManagerInitializer implements Initializer
{
    #[Singleton]
    public function initialize(Container $container): SessionManager
    {
        return $container->get(AppSessionManager::class);
    }
}
