<x-mail::message>
    # {{ $title }}

    {{ $body }}

    @if ($bookId)
        <x-mail::button :url="config('app.url') . '/books/' . $bookId">
            Read the book
        </x-mail::button>
    @endif

    Best regards,
    {{ config('app.name') }}
</x-mail::message>
