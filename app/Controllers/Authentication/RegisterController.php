<?php

declare(strict_types=1);

namespace App\Controllers\Authentication;

use App\CommandBus\Authentication\LoginUser;
use App\CommandBus\Authentication\RegisterUser;
use App\Config\Definitions\AuthConfig;
use App\Requests\Authentication\RegisterRequest;
use Inertia\Response;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\Get;
use Tempest\Router\Post;

use function Tempest\CommandBus\command;

final readonly class RegisterController
{
    public function __construct(
        private AuthConfig $authConfig,
    ) {}

    #[Get('/register')]
    public function create(): Response
    {
        return inertia(component: 'Authentication/Register');
    }

    #[Post('/register')]
    public function store(RegisterRequest $request): Response|Redirect
    {
        if ($request->password !== $request->password_confirmation) {
            return inertia(
                component: 'Authentication/Register',
                props: ['errors' => ['password_confirmation' => 'Passwords do not match.']],
            );
        }

        command(new RegisterUser(
            name: $request->name,
            email: $request->email,
            password: $request->password,
        ));

        if ($this->authConfig->loginAfterRegister) {
            command(new LoginUser(
                email: $request->email,
                password: $request->password,
            ));
        }

        return new Redirect('/');
    }
}
