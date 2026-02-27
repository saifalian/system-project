<?php
namespace App\Config;

class GoogleOAuth {
    public static function getConfig() {
        $redirectUri = Config::get('GOOGLE_REDIRECT_URL');
        // Encode spaces in path (Google requires valid URI)
        $parts = explode('?', $redirectUri, 2);
        $encodedPath = str_replace(' ', '%20', $parts[0]);
        $redirectUri = $encodedPath . (isset($parts[1]) ? '?' . $parts[1] : '');

        return [
            'client_id' => Config::get('GOOGLE_CLIENT_ID'),
            'client_secret' => Config::get('GOOGLE_CLIENT_SECRET'),
            'redirect_uri' => $redirectUri,
            'scopes' => [
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile'
            ]
        ];
    }
}
