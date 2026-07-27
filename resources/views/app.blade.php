<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ShortEarn</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
</head>
<body class="app-shell">
    <main class="card">
        <div class="brand">
            <div class="brand__logo">S</div>
            <div>
                <h1>Welcome to ShortEarn</h1>
                <p>Telegram Shortlink Earning Mini App</p>
            </div>
        </div>

        @if($user)
            <div class="profile">
                <div class="profile__title">Logged in as</div>
                <div class="profile__name">
                    {{ $user['first_name'] ?? 'Telegram User' }}
                    {{ $user['last_name'] ?? '' }}
                </div>
                <div class="profile__meta">
                    ID: {{ $user['id'] ?? '—' }}
                </div>
                <div class="profile__meta">
                    Username: @{{ $user['username'] ?? 'N/A' }}
                </div>
            </div>

            <a class="primary-btn" href="{{ route('dashboard') }}">Go to Dashboard</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="secondary-btn" type="submit">Logout</button>
            </form>
        @else
            <p class="hint">
                Open this page inside Telegram, then tap the login button.
            </p>

            <button id="loginBtn" class="primary-btn" type="button">Login with Telegram</button>

            <div class="profile profile--compact">
                <div class="profile__title">Telegram app detected</div>
                <div class="profile__meta" id="telegramStatus">Waiting for user data…</div>
            </div>

            @if(!$telegramBotTokenExists)
                <div class="alert">
                    TELEGRAM_BOT_TOKEN is missing in <code>.env</code>.
                </div>
            @endif
        @endif
    </main>

    <script>
        window.ShortEarn = {
            loginUrl: @json(route('telegram.login')),
            dashboardUrl: @json(route('dashboard')),
            csrf: @json(csrf_token()),
        };
    </script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>
