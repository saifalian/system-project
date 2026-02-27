<?php
namespace App\Utils;

class Crypto {
    public static function generateOtp($length = 6) {
        // Generate a random string of numbers
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= random_int(0, 9);
        }
        return $otp;
    }

    public static function hashOtp($otp) {
        return password_hash($otp, PASSWORD_DEFAULT);
    }

    public static function verifyOtp($otp, $hash) {
        return password_verify($otp, $hash);
    }
}
