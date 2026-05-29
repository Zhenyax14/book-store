@props(['book'])

@php
    $colors = ['orange', 'blue', 'green', 'red', 'purple', 'teal', 'dark'];
    $color  = $colors[crc32($book['slug']) % count($colors)];
    $price  = $book['is_free'] ? 'Free' : ($book['price']['formatted'] ?? '—');
    $isSubscription = ($book['access_type'] ?? '') === 'subscription';
@endphp

<a href="/books/{{ $book['id'] }}" class="product-card h-100 d-block text-decoration-none">

    <div class="product-card__img">
        @if(!empty($book['cover_url']))
            <img src="{{ $book['cover_url'] }}"
                 alt="{{ $book['title'] }}"
                 style="height:160px; width:auto; object-fit:cover; border-radius:4px;"
                 loading="lazy">
        @else
            <div class="book-thumb book-thumb--{{ $color }} mx-auto">
                <span>{{ $book['title'] }}</span>
                @if(!empty($book['publisher']))
                    <small>{{ $book['publisher'] }}</small>
                @endif
            </div>
        @endif

        <div class="product-card__overlay">
            @if($isSubscription)
                <button class="product-card__btn product-card__btn--cart">⭐ Subscribe</button>
            @else
                <button class="product-card__btn product-card__btn--cart">📖 Read</button>
            @endif
        </div>
    </div>

    <div class="product-card__info">
        <h3 class="product-card__title">{{ $book['title'] }}</h3>
        @if(!empty($book['publisher']))
            <p class="product-card__author">{{ $book['publisher'] }}</p>
        @endif
        <div class="product-card__price">{{ $price }}</div>
    </div>

</a>
