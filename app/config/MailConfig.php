<?php
namespace App\Config;

class MailConfig {
    public static function getToken() {
        return Config::get('MAILTRAP_TOKEN');
    }
}
