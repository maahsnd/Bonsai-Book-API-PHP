<?php

class Utilities
{
    public static function sanitize($input)
    {
        if ($input) {
            trim($input);
            stripslashes($input);
            htmlspecialchars($input);
        } else {
            $input = '';
        }
        return $input;
    }
    public static function trimAndEsc($input)
    {

        if ($input) {
            trim($input);
            htmlspecialchars($input);
        } else {
            $input = '';
        }
        return $input;
    }
    // Minimal implementation for development purposes
    public static function validateUser($providedCredentials, $requestedCredentials) {
        if ($providedCredentials === $requestedCredentials) return true;
        else {
            return false;
        }
    }
}
