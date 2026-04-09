<?php

declare(strict_types=1);

namespace App\Controllers\Authentication;

use App\CommandBus\Authentication\LogoutUser;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\Post;

use function Tempest\CommandBus\command;

final readonly class LogoutController
{
    #[Post('/logout')]
    public function __invoke(): Redirect
    {
        command(new LogoutUser());

        return new Redirect('/');
    }
}
