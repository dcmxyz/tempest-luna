<?php

declare(strict_types=1);

namespace App\Middleware;

use Inertia\Middleware\Middleware;
use Tempest\Support\Priority;

use function Tempest\env;

#[Priority(Priority::HIGH)]
class HandleInertiaRequests extends Middleware
{
    /**
     * The inertia base template is not discovered automatically.
     * Override here.
     */
    protected string $rootView = '/Resources/app.view.php';

    public function share(): array
    {
        return [
            ...parent::share(),
            'app' => $this->appShared(),
            'flash' => $this->inertia->always(fn () => [
                'success' => $this->session->get('success'),
            ]),
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
