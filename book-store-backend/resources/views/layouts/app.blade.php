<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bookshop')</title>
    @yield('meta')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,400;0,500;0,600;0,700&family=Barlow+Condensed:wght@600;700&family=Playfair+Display:ital,wght@0,700;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.scss', 'resources/js/app.js'])

    <!--- SEO ---->
    <meta name="google-site-verification" content="CQFPXAvG-uUVH7j7UPlVPMJfY4xuB5lMM83pms-QQ2Q" />
</head>
<body>

<header class="header">
    <div class="header__inner container">

        <a href="{{ route('catalog.index') }}" class="logo text-decoration-none">
            <span class="logo__book">Book</span><span class="logo__shop">store</span>
        </a>

        <nav class="nav" aria-label="Main navigation">
            <a href="{{ route('catalog.index') }}"
               class="nav__link {{ request()->routeIs('catalog.index') ? 'nav__link--active' : '' }}">
                CATALOG
            </a>
            <a href="/search" class="nav__link">SEARCH</a>
        </nav>

        <div class="header__actions" id="header-actions">

            {{-- Guest --}}
            <a href="/auth/login"    class="nav__link" id="btn-login">Login</a>
            <a href="/auth/register" class="nav__link" id="btn-register">Register</a>

            {{-- Auth (oculto hasta que JS lo active) --}}
            <a href="/reader/reading-list" class="header__wishlist" id="btn-mybooks" style="display:none">
                My Books
            </a>

            <a href="/reader/cart" class="cart-btn" id="btn-cart" style="display:none">
                <div class="cart-btn__icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <span id="cart-count" style="display:none" class="cart-btn__count"></span>
                </div>
                <div class="cart-btn__info">
                    <span class="cart-btn__label">Cart</span>
                    <span id="cart-total" style="display:none" class="cart-btn__total"></span>
                </div>
            </a>

            <button class="header__logout" id="btn-logout" style="display:none">Logout</button>

        </div>
    </div>
</header>

<main id="main-content" class="container" style="padding-top: 32px; padding-bottom: 48px;">
    @yield('content')
</main>

<footer class="footer">
    <div class="footer__inner container">
        <div>
            <a href="{{ route('catalog.index') }}" class="footer__logo logo text-decoration-none">
                <span class="logo__book">Book</span><span class="logo__shop">store</span>
            </a>
            <p class="footer__desc">Your digital bookstore. Over 50,000 titles available instantly.</p>
        </div>
        <div>
            <p class="footer__heading">Store</p>
            <ul class="footer__links">
                <li><a href="{{ route('catalog.index') }}">Catalog</a></li>
                <li><a href="/search">Search</a></li>
            </ul>
        </div>
        <div>
            <p class="footer__heading">Account</p>
            <ul class="footer__links">
                <li><a href="/auth/login">Login</a></li>
                <li><a href="/auth/register">Register</a></li>
                <li><a href="/reader/cart">My cart</a></li>
                <li><a href="/reader/reading-list">Reading list</a></li>
            </ul>
        </div>
        <div>
            <p class="footer__heading">Contact</p>
            <ul class="footer__contact">
                <li>📞 900 000 000</li>
                <li>📚 +50,000 titles</li>
                <li>🔒 Secure payment</li>
            </ul>
        </div>
    </div>
    <div class="footer__bottom">
        © {{ date('Y') }} Bookstore. All rights reserved.
    </div>
</footer>

@stack('scripts')

<script>
    (function () {
        try {
            const token = localStorage.getItem('auth_token')
            if (!token) return

            // Activar UI autenticada
            document.getElementById('btn-login').style.display    = 'none'
            document.getElementById('btn-register').style.display = 'none'
            document.getElementById('btn-mybooks').style.display  = ''
            document.getElementById('btn-cart').style.display     = 'flex'
            document.getElementById('btn-logout').style.display   = ''

            // Logout
            document.getElementById('btn-logout').addEventListener('click', function () {
                fetch('/api/v1/auth/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json',
                    },
                }).finally(function () {
                    localStorage.removeItem('auth_token')
                    localStorage.removeItem('auth_user')
                    window.location.reload()
                })
            })

            // Cargar carrito
            fetch('/api/v1/cart', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                },
            })
                .then(function (r) { return r.ok ? r.json() : null })
                .then(function (data) {
                    if (!data) return
                    const count = data.items_count ?? data.items?.length ?? 0
                    const total = data.total?.formatted ?? null

                    if (count > 0) {
                        const elCount = document.getElementById('cart-count')
                        elCount.textContent  = count
                        elCount.style.display = ''
                    }
                    if (total) {
                        const elTotal = document.getElementById('cart-total')
                        elTotal.textContent  = 'Total: ' + total
                        elTotal.style.display = ''
                    }
                })
                .catch(function () {})

        } catch (e) {}
    })()
</script>

</body>
</html>
