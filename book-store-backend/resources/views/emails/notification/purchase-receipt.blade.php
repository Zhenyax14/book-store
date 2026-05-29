<x-mail::message>
    # {{ $title }}

    {{ $body }}

    @if ($bookId)
        <x-mail::panel>
            The book is available in your library. Enjoy your reading!
        </x-mail::panel>

        <x-mail::button :url="config('app.url') . '/books/' . $bookId">
            Start reading
        </x-mail::button>
    @endif

    If you did not make this purchase, please contact support.

    Best regards,
    {{ config('app.name') }}
</x-mail::message>
