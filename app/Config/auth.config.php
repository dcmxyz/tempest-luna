<?php

declare(strict_types=1);

namespace App\Config;

use App\Config\Definitions\AuthConfig;

use function Tempest\env;

return new AuthConfig(
    loginAfterRegister: env('AUTH_LOGIN_AFTER_REGISTER', true),
);
