<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class TelegramMiniAppController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $user = session('shortearn_user');

        if ($user && !$request->boolean('force_login')) {
            return redirect()->route('dashboard');
        }

        return view('app', [
            'user' => $user,
            'telegramBotTokenExists' => filled(config('shortearn.bot_token')),
        ]);
    }

    public function dashboard(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $user = session('shortearn_user');

        if (!$user) {
            return redirect()->route('home');
        }

        $stats = $this->demoStatsFor((int) ($user['id'] ?? 0));

        return view('dashboard', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'initData' => ['required', 'string'],
        ]);

        $botToken = (string) config('shortearn.bot_token');

        if ($botToken === '') {
            return response()->json([
                'ok' => false,
                'message' => 'TELEGRAM_BOT_TOKEN is missing in .env',
            ], 500);
        }

        try {
            $payload = $this->verifyAndParseInitData($request->string('initData')->toString(), $botToken);
        } catch (RuntimeException $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $user = $payload['user'];

        $sessionUser = [
            'id' => $user['id'] ?? null,
            'first_name' => $user['first_name'] ?? 'Telegram User',
            'last_name' => $user['last_name'] ?? null,
            'username' => $user['username'] ?? null,
            'language_code' => $user['language_code'] ?? null,
            'photo_url' => $user['photo_url'] ?? null,
            'auth_date' => $payload['auth_date'] ?? null,
        ];

        session([
            'shortearn_user' => $sessionUser,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Login successful',
            'redirect' => route('dashboard'),
            'user' => $sessionUser,
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('shortearn_user');

        return redirect()->route('home');
    }

    private function verifyAndParseInitData(string $initData, string $botToken): array
    {
        parse_str($initData, $data);

        if (!is_array($data) || empty($data)) {
            throw new RuntimeException('Invalid Telegram initData.');
        }

        if (!isset($data['hash'])) {
            throw new RuntimeException('Telegram hash is missing.');
        }

        $receivedHash = (string) $data['hash'];
        unset($data['hash']);

        if (isset($data['signature'])) {
            unset($data['signature']);
        }

        if (!isset($data['auth_date'])) {
            throw new RuntimeException('auth_date is missing.');
        }

        $authDate = (int) $data['auth_date'];
        if ($authDate <= 0) {
            throw new RuntimeException('auth_date is invalid.');
        }

        // Reject very old payloads.
        $maxAgeSeconds = 86400; // 24 hours
        if (abs(time() - $authDate) > $maxAgeSeconds) {
            throw new RuntimeException('Telegram initData is too old.');
        }

        ksort($data, SORT_STRING);

        $dataCheckString = collect($data)
            ->map(function ($value, $key) {
                if (is_array($value)) {
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
                return $key . '=' . $value;
            })
            ->implode("\n");

        // Telegram Mini Apps validation rule:
        // secret_key = HMAC_SHA256(bot_token, "WebAppData")
        // hash = HMAC_SHA256(data_check_string, secret_key)
        $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);
        $calculatedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        if (!hash_equals($calculatedHash, $receivedHash)) {
            throw new RuntimeException('Telegram hash verification failed.');
        }

        $user = [];

        if (!empty($data['user'])) {
            $decoded = json_decode($data['user'], true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $user = $decoded;
            }
        }

        if (empty($user['id'])) {
            throw new RuntimeException('Telegram user data not found.');
        }

        return [
            'auth_date' => $authDate,
            'user' => $user,
            'data' => $data,
        ];
    }

    private function demoStatsFor(int $telegramId): array
    {
        $seed = $telegramId > 0 ? $telegramId : 1;

        $balance = number_format((($seed % 3000) / 100) + 1.25, 2, '.', '');
        $bonus = number_format((($seed % 700) / 100) + 0.25, 2, '.', '');
        $referrals = ($seed % 27) + 2;
        $tasks = ($seed % 8) + 1;
        $withdrawable = number_format(((int) $balance * 100 + (int) $bonus * 100) / 100, 2, '.', '');

        return [
            'balance' => $balance,
            'bonus' => $bonus,
            'referrals' => $referrals,
            'tasks_completed' => $tasks,
            'withdrawable' => $withdrawable,
        ];
    }
}
