<?php
namespace App\Utils;

class Validator {
    public static function sanitize($input) {
        return htmlspecialchars(strip_tags(trim($input)));
    }

    public static function isEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function required($fields, $data) {
        $errors = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[] = "$field is required.";
            }
        }
        return $errors;
    }
}
