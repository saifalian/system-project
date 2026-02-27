<?php
namespace App\Services;

class EmailService {
    public static function sendOtp($email, $otpCode) {
        // Load SMTP credentials from environment — never hardcode secrets
        $host     = $_ENV['MAIL_HOST']     ?? 'sandbox.smtp.mailtrap.io';
        $port     = (int)($_ENV['MAIL_PORT']     ?? 587);
        $username = $_ENV['MAIL_USERNAME'] ?? '';
        $password = $_ENV['MAIL_PASSWORD'] ?? '';
        $from     = $_ENV['MAIL_FROM']     ?? 'noreply@systemsocialmedia.local';
        $fromName = $_ENV['MAIL_FROM_NAME'] ?? 'System Social Media MFA';
        $subject  = 'Your OTP Login Code';
        $body     = "Hello,\r\n\r\nYour secure OTP code is: {$otpCode}\r\n\r\nThis code expires in 10 minutes.\r\n\r\n-- System Social Media MFA";

        try {
            // Open TCP socket
            $socket = fsockopen($host, $port, $errno, $errstr, 10);
            if (!$socket) {
                error_log("Mail socket error: {$errstr} ({$errno})");
                return false;
            }

            $read = fgets($socket, 512);
            if (substr($read, 0, 3) !== '220') { fclose($socket); error_log("Mail: bad greeting: $read"); return false; }

            // EHLO
            fwrite($socket, "EHLO localhost\r\n");
            $ehloResponse = '';
            while (($line = fgets($socket, 512)) !== false) {
                $ehloResponse .= $line;
                if (substr($line, 3, 1) === ' ') break;
            }

            // STARTTLS
            fwrite($socket, "STARTTLS\r\n");
            $r = fgets($socket, 512);

            // Upgrade to TLS
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

            // EHLO again after TLS
            fwrite($socket, "EHLO localhost\r\n");
            while (($line = fgets($socket, 512)) !== false) {
                if (substr($line, 3, 1) === ' ') break;
            }

            // AUTH LOGIN
            fwrite($socket, "AUTH LOGIN\r\n");
            fgets($socket, 512);
            fwrite($socket, base64_encode($username) . "\r\n");
            fgets($socket, 512);
            fwrite($socket, base64_encode($password) . "\r\n");
            $authResponse = fgets($socket, 512);
            if (substr($authResponse, 0, 3) !== '235') {
                error_log("Mail AUTH failed: {$authResponse}");
                fclose($socket);
                return false;
            }

            // MAIL FROM
            fwrite($socket, "MAIL FROM:<{$from}>\r\n");
            fgets($socket, 512);

            // RCPT TO
            fwrite($socket, "RCPT TO:<{$email}>\r\n");
            fgets($socket, 512);

            // DATA
            fwrite($socket, "DATA\r\n");
            fgets($socket, 512);

            $message  = "From: {$fromName} <{$from}>\r\n";
            $message .= "To: {$email}\r\n";
            $message .= "Subject: {$subject}\r\n";
            $message .= "MIME-Version: 1.0\r\n";
            $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $message .= "\r\n";
            $message .= $body;
            $message .= "\r\n.";

            fwrite($socket, $message . "\r\n");
            $dataResponse = fgets($socket, 512);

            // QUIT
            fwrite($socket, "QUIT\r\n");
            fclose($socket);

            if (substr($dataResponse, 0, 3) === '250') {
                return true;
            } else {
                error_log("Mail DATA error: {$dataResponse}");
                return false;
            }
        } catch (\Exception $e) {
            error_log("Mail exception: " . $e->getMessage());
            return false;
        }
    }
}
