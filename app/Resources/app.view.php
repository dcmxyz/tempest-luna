<!doctype html>
<html lang="en" class="bg-ui-template-background">
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

        <link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg" />
        <link rel="shortcut icon" href="/favicon/favicon.ico" />
        <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png" />
        <link rel="manifest" href="/favicon/site.webmanifest" />

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=atkinson-hyperlegible-next:200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i" rel="stylesheet" />

        <x-inertia-head>
            <title>{{ Tempest\env('APP_TITLE', default: 'Luna') }}</title>
        </x-inertia-head>

        <x-vite-tags />
    </head>
    <body class="h-dvh w-full text-ui-text p-4 antialiased">
        <x-inertia-app />
    </body>
</html>
