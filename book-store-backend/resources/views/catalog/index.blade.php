@extends('layouts.app')

@section('title', 'Featured — Bookshop')

@section('content')
    <div class="content">
        <section class="section">

            <div class="section__header">
                <h2 class="section__title">FEATURED</h2>
                <p class="section__subtitle">
                    A carefully curated selection of the best titles from our bookstore
                </p>
                <div class="section__line"></div>
            </div>

            <form method="GET" action="{{ route('catalog.index') }}" class="filters-bar" id="filters-form">
                <div class="filters-right">
                    {{-- DESPUÉS: poner esto --}}
                    <select name="tag" class="sort-select" onchange="this.form.submit()">
                        <option value="" @selected(empty($filters['tag']))>All tags</option>
                        @foreach($tags ?? [] as $tag)
                            <option value="{{ $tag->slug }}" @selected(($filters['tag'] ?? '') === $tag->slug)>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="access_type" class="sort-select" onchange="this.form.submit()">
                        <option value=""             @selected(empty($filters['access_type']))>All access types</option>
                        <option value="free"         @selected(($filters['access_type'] ?? '') === 'free')>Free</option>
                        <option value="purchase"     @selected(($filters['access_type'] ?? '') === 'purchase')>Purchase</option>
                        <option value="subscription" @selected(($filters['access_type'] ?? '') === 'subscription')>Subscription</option>
                    </select>
                </div>
            </form>

            <div class="products-wrapper">
                @if(!empty($books))
                    <div class="products-grid">
                        @foreach($books as $book)
                            <x-book.book-card :book="$book" />
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-5">No books found for the selected filters.</p>
                @endif
            </div>

            {{-- Paginación page-based (meta de BookCollectionResource) --}}
            @php
                $currentPage = $meta['current_page'] ?? 1;
                $totalPages  = $meta['total_pages']  ?? 1;
            @endphp

            @if($totalPages > 1)
                <nav class="d-flex justify-content-center gap-2 mt-4" aria-label="Catalog pages">

                    @if($currentPage > 1)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}"
                           class="btn btn-outline-secondary btn-sm" rel="prev">‹</a>
                    @endif

                    @for($p = max(1, $currentPage - 2); $p <= min($totalPages, $currentPage + 2); $p++)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $p]) }}"
                           class="btn btn-sm {{ $p === $currentPage ? 'btn-warning' : 'btn-outline-secondary' }}">
                            {{ $p }}
                        </a>
                    @endfor

                    @if($currentPage < $totalPages)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}"
                           class="btn btn-outline-secondary btn-sm" rel="next">›</a>
                    @endif

                </nav>
            @endif

        </section>
    </div>
@endsection
