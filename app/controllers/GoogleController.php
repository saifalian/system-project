<?php
namespace App\Controllers;

use App\Config\GoogleOAuth;
use App\Services\UserService;
use App\Services\TokenService;
use App\Utils\Response;

class GoogleController {
    /**
     * SSL verification: enabled in production, disabled only in local dev.
     * Disabling in production would expose the app to MITM attacks.
     */
    private static function sslVerify(): bool {
        return ($_ENV['APP_ENV'] ?? 'development') === 'production';
    }
    public function redirect() {
        // Store whether this was triggered from the login or register page
        $action = $_GET['action'] ?? 'login';
        $_SESSION['google_action'] = ($action === 'register') ? 'register' : 'login';

        $config = GoogleOAuth::getConfig();
        $url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
            'client_id'     => $config['client_id'],
            'redirect_uri'  => $config['redirect_uri'],
            'response_type' => 'code',
            'scope'         => implode(' ', $config['scopes']),
            'access_type'   => 'offline',
            'prompt'        => 'consent'
        ]);

        Response::redirect($url);
    }

    public function callback() {
        if (!isset($_GET['code'])) {
            $_SESSION['error'] = "Google login failed: Code not provided.";
            Response::redirect('index.php?route=auth/login');
        }

        $config = GoogleOAuth::getConfig();
        $code = $_GET['code'];

        // Exchange code for token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://oauth2.googleapis.com/token");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id'     => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'redirect_uri'  => $config['redirect_uri'],
            'grant_type'    => 'authorization_code',
            'code'          => $code
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, self::sslVerify());

        $response = curl_exec($ch);
        curl_close($ch);

        $tokenData = json_decode($response, true);

        if (isset($tokenData['error'])) {
            $_SESSION['error'] = "Google login failed: " . $tokenData['error_description'];
            Response::redirect('index.php?route=auth/login');
        }

        // Get user info from Google
        $accessToken = $tokenData['access_token'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/oauth2/v2/userinfo");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $accessToken]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, self::sslVerify());
        $userInfoResponse = curl_exec($ch);
        curl_close($ch);

        $googleUser = json_decode($userInfoResponse, true);

        if (!isset($googleUser['email'])) {
            $_SESSION['error'] = "Failed to retrieve email from Google.";
            Response::redirect('index.php?route=auth/login');
        }

        // Read and clear the action flag
        $action = $_SESSION['google_action'] ?? 'login';
        unset($_SESSION['google_action']);

        if ($action === 'register') {
            // Check if they already had an account before we create one
            $alreadyExisted = (bool) UserService::findGoogleUser($googleUser);

            // SIGNUP flow: create account if it doesn't exist yet
            $user = UserService::findOrCreateGoogleUser($googleUser);
            if (!$user) {
                $_SESSION['error'] = "Google sign-up failed. Please try again.";
                Response::redirect('index.php?route=auth/register');
            }

            if (!$alreadyExisted) {
                // Brand-new account — show success popup, let them log in manually
                $_SESSION['success'] = '✅ Account registered successfully! Please log in.';
                Response::redirect('index.php?route=auth/login');
            }
            // Already had an account — fall through to login below
        } else {
            // LOGIN flow: only allow already-registered accounts
            $user = UserService::findGoogleUser($googleUser);
            if (!$user) {
                $_SESSION['signup_required'] = true;
                Response::redirect('index.php?route=auth/login');
            }
        }

        TokenService::createLoginSession($user);
        Response::redirect('index.php?route=dashboard/home');
    }
}
