document.addEventListener('DOMContentLoaded', () => {
    const tg = window.Telegram?.WebApp;

    if (tg) {
        try {
            tg.ready();
            tg.expand();
            tg.setHeaderColor?.('#0f172a');
            tg.setBackgroundColor?.('#0f172a');
        } catch (e) {
            console.warn('Telegram WebApp init warning:', e);
        }
    }

    const loginBtn = document.getElementById('loginBtn');
    const statusEl = document.getElementById('telegramStatus');

    const showStatus = (text) => {
        if (statusEl) {
            statusEl.textContent = text;
        }
    };

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const login = async () => {
        if (!tg) {
            showStatus('Open this app inside Telegram.');
            return;
        }

        const initData = tg.initData || '';
        const user = tg.initDataUnsafe?.user;

        if (user) {
            showStatus(`User detected: ${user.first_name || ''} ${user.last_name || ''}`.trim());
        } else {
            showStatus('No Telegram user data found yet.');
        }

        if (!initData) {
            showStatus('Telegram initData is empty. Open the Mini App from Telegram.');
            return;
        }

        if (loginBtn) {
            loginBtn.disabled = true;
            loginBtn.textContent = 'Logging in...';
        }

        try {
            const response = await fetch(window.ShortEarn.loginUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ initData }),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok || !data.ok) {
                throw new Error(data.message || 'Login failed.');
            }

            if (window.ShortEarn?.dashboardUrl && data.redirect) {
                window.location.href = data.redirect;
                return;
            }

            window.location.href = window.ShortEarn.dashboardUrl;
        } catch (error) {
            console.error(error);
            showStatus(error.message || 'Login failed.');
            if (loginBtn) {
                loginBtn.disabled = false;
                loginBtn.textContent = 'Login with Telegram';
            }
        }
    };

    if (loginBtn) {
        loginBtn.addEventListener('click', login);
    }

    // Try auto-login once in Telegram.
    if (tg?.initData) {
        const user = tg.initDataUnsafe?.user;
        if (statusEl) {
            if (user) {
                showStatus(`Hello ${user.first_name || 'Telegram User'}!`);
            } else {
                showStatus('Telegram connected. Ready to log in.');
            }
        }
    } else if (statusEl) {
        showStatus('Open this page inside Telegram to continue.');
    }
});
