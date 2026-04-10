<?php

declare(strict_types=1);

namespace App\Middleware;

use Inertia\Middleware\Middleware;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Core\Priority;

use Tempest\Http\Session\FormSession;
use function Tempest\Container\get;
use function Tempest\env;

#[Priority(Priority::FRAMEWORK - 20)]
class HandleInertiaRequests extends Middleware
{
    /**
     * The inertia base template is not discovered automatically.
     * Override here.
     */
    protected string $rootView = '/Resources/app.view.php';

    public function share(): array
    {
        return array_replace_recursive(
            parent::share(),
            [
                'auth' => $this->authShared(),
                'app' => $this->appShared(),
            ],
        );
    }

    private function authShared(): array
    {
        return [
            'user' => static fn (Authenticator $auth) => $auth->current(),
        ];
    }

    private function appShared(): array
    {
        return [
            'name' => env('APP_TITLE', default: 'Luna'),
            'baseUri' => env('BASE_URI', default: '127.0.0.1'),
        ];
    }

    protected function reflash(): void
    {
        // Prevent validation errors from surviving across redirects.
        // ResolveErrorProps runs lazily after reflash(), so errors must be cleared here.
        $this->session->remove('#validation_errors');
        $this->session->remove('#original_values');

        $this->session->reflash();
    }
}
