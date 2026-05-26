<x-mail::message>
    # {{ $title }}

    {{ $body }}

    @if ($bookId)
        <x-mail::table>
            | | |
            |:--|:--|
            | Status | ✓ Completed |
        </x-mail::table>

        <x-mail::button :url="config('app.url') . '/books/' . $bookId">
            Leave a review
        </x-mail::button>
    @endif

    Best regards,
    {{ config('app.name') }}
</x-mail::message>
