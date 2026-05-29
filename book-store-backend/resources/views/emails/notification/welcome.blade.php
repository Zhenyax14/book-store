<x-mail::message>
    # {{ $title }}

    {{ $body }}

    Open the catalog and find your first book — it's already waiting for you.

    <x-mail::button :url="config('app.url') . '/catalog'">
        Go to catalog
    </x-mail::button>

    Best regards,
    {{ config('app.name') }}
</x-mail::message>
