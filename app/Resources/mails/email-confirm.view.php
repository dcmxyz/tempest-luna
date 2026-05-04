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
        Please click the button below to confirm your email address. It expires in 15 minutes.
    </x-mail-paragraph>

    <x-mail-button :url="$verificationUri">
        Confirm your email address
    </x-mail-button>

    <x-mail-paragraph>
        If you cannot click the button, copy and paste the following link into your browser:
        <br />
        <span style="font-size: 14px; opacity: 0.5;">{{ $verificationUri }}</span>
    </x-mail-paragraph>

    <x-mail-paragraph>
        If you didn't create an account, you can safely ignore this email.
    </x-mail-paragraph>
</x-mail>