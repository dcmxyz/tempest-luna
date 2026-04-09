<?php

declare(strict_types=1);

namespace App\Controllers\Authentication;

use App\CommandBus\Authentication\PasswordResetEmail;
use App\Requests\Authentication\ForgotPasswordRequest;
use Inertia\Response;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\Get;
use Tempest\Router\Post;

use function Tempest\CommandBus\command;
use function Tempest\Router\uri;

final readonly class ForgotPasswordController
{
    #[Get('/forgot-password')]
    public function show(): Response
    {
        return inertia(component: 'Authentication/ForgotPassword');
    }

    #[Post('/forgot-password')]
    public function send(ForgotPasswordRequest $request): Redirect
    {
        command(new PasswordResetEmail(
            email: $request->email,
        ));

        return new Redirect(uri([self::class, 'show']));
    }
}