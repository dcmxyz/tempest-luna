<?php

declare(strict_types=1);

namespace App\Controllers\Authentication;

use App\Services\AuthService;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\Post;

final readonly class LogoutController
{
    public function __construct(
        private AuthService $authService,
    ) {}

    #[Post('/logout')]
    public function __invoke(): Redirect
    {
        $this->authService->logout();

        return new Redirect('/');
    }
}
