<x-mail>
    <x-mail-title>
        <strong style="font-size:20px;">
            {{ Tempest\env('APPLICATION_NAME', 'Luna') }}
        </strong>
    </x-mail-title>

    <x-mail-paragraph>
        Hello {{ $user->name }},
    </x-mail-paragraph>

    <x-mail-paragraph>
        Please click the button below to reset your password. It expires in 1 hour.
    </x-mail-paragraph>

    <x-mail-button :url="$resetUrl">
        Reset your password
    </x-mail-button>

    <x-mail-paragraph>
        If you cannot click the button, copy and paste the following link into your browser:
        <br />
        <span style="font-size: 14px; opacity: 0.5;">{{ $resetUrl }}</span>
    </x-mail-paragraph>

    <x-mail-paragraph>
        If you didn't request this, you can safely ignore this email.
    </x-mail-paragraph>
</x-mail>
