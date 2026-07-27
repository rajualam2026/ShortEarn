<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ShortEarn Dashboard</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
</head>
<body class="app-shell app-shell--dashboard">
    <main class="dashboard">
        <section class="hero card">
            <div class="hero__top">
                <div>
                    <div class="tag">Dashboard</div>
                    <h1>Welcome, {{ $user['first_name'] ?? 'User' }}</h1>
                    <p>Your Mini App is now connected.</p>
                </div>
                <div class="avatar">
                    {{ strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) }}
                </div>
            </div>

            <div class="profile__meta">
                ID: {{ $user['id'] ?? '—' }}
            </div>
            <div class="profile__meta">
                Username: @{{ $user['username'] ?? 'N/A' }}
            </div>
        </section>

        <section class="stats-grid">
            <div class="stat card">
                <span>Balance</span>
                <strong>${{ $stats['balance'] }}</strong>
            </div>
            <div class="stat card">
                <span>Daily Bonus</span>
                <strong>${{ $stats['bonus'] }}</strong>
            </div>
            <div class="stat card">
                <span>Referrals</span>
                <strong>{{ $stats['referrals'] }}</strong>
            </div>
            <div class="stat card">
                <span>Tasks Done</span>
                <strong>{{ $stats['tasks_completed'] }}</strong>
            </div>
        </section>

        <section class="card section">
            <h2>Quick Actions</h2>
            <div class="action-list">
                <button class="secondary-btn" type="button">Start Earning</button>
                <button class="secondary-btn" type="button">Daily Bonus</button>
                <button class="secondary-btn" type="button">Withdraw</button>
                <button class="secondary-btn" type="button">Referral Link</button>
            </div>
        </section>

        <section class="card section">
            <h2>Mini App Status</h2>
            <p class="hint">This starter uses Telegram initData validation and a Laravel session login.</p>
            <p class="hint">Current withdrawable amount: <strong>${{ $stats['withdrawable'] }}</strong></p>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="primary-btn" type="submit">Logout</button>
            </form>
        </section>
    </main>
</body>
</html>
