<?php
namespace App\Services;

use App\Models\Otp;
use App\Utils\Crypto;

class OtpService {
    public static function generateAndSave($userId) {
        // Delete previous OTPs for this user
        Otp::deleteByUserId($userId);

        $otpCode = Crypto::generateOtp(6);
        $hashedOtp = Crypto::hashOtp($otpCode);
        
        // Expires in 5 minutes
        $expiresAt = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        
        Otp::create($userId, $hashedOtp, $expiresAt);
        
        return $otpCode;
    }

    public static function verify($userId, $inputOtp) {
        $otpRecord = Otp::findLatestByUserId($userId);
        
        if (!$otpRecord) {
            return ['success' => false, 'message' => 'No OTP found. Please request a new one.'];
        }

        // Check expiration
        if (strtotime($otpRecord['expires_at']) < time()) {
            return ['success' => false, 'message' => 'OTP has expired. Please request a new one.'];
        }

        // Check attempts — max 3
        if ($otpRecord['attempts'] >= 3) {
            Otp::deleteByUserId($userId);
            return ['success' => false, 'max_attempts_exceeded' => true, 'message' => 'Too many failed attempts.'];
        }

        // Verify
        if (Crypto::verifyOtp($inputOtp, $otpRecord['otp_hash'])) {
            Otp::deleteByUserId($userId);
            return ['success' => true];
        } else {
            $newAttempts = Otp::incrementAttempts($otpRecord['id']);
            $remaining = 3 - ($otpRecord['attempts'] + 1);
            if ($remaining <= 0) {
                Otp::deleteByUserId($userId);
                return ['success' => false, 'max_attempts_exceeded' => true, 'message' => 'Too many failed attempts.'];
            }
            return ['success' => false, 'message' => "Invalid OTP. {$remaining} attempt(s) remaining."];
        }
    }
}
