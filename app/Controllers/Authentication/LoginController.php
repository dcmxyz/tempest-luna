<?php

declare(strict_types=1);

namespace App\Controllers\Authentication;

use App\Middleware\RedirectIfAuthenticated;
use App\Requests\Authentication\LoginRequest;
use App\Services\AuthService;
use Inertia\Response;
use Inertia\ResponseFactory;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\Get;
use Tempest\Router\Post;
use Tempest\Validation\Exceptions\ValidationFailed;

use function App\Helpers\fail_validation;

final readonly class LoginController
{
    public function __construct(
        private AuthService $authService,
    ) {}

    #[Get('/login', middleware: [RedirectIfAuthenticated::class])]
    public function show(): Response
    {
        return inertia(component: 'Authentication/Login');
    }

    /**
     * @throws ValidationFailed
     */
    #[Post('/login', middleware: [RedirectIfAuthenticated::class])]
    public function store(LoginRequest $request): Response|ResponseFactory|Redirect
    {
        $loggedIn = $this->authService->login(
            email: $request->email,
            password: $request->password,
            remember: true,
        );

        if (! $loggedIn) {
            fail_validation(
                field: 'email',
                message: 'These credentials do not match our records.',
            );
        }

        return new Redirect('/');
    }
}
