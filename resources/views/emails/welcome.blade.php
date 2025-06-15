<x-mail::message>
    # Welcome to {{ config('app.name') }}!

    Hello {{ $user->name }},

    Thank you for registering with us. We're excited to have you as part of our community!

    <x-mail::button :url="url('/')">
        Visit Our Website
    </x-mail::button>

    Best regards,<br>
    {{ config('app.name') }} Team
</x-mail::message>
