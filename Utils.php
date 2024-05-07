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
}
