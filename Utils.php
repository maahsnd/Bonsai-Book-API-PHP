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
    public static function validateUser($requestedCredentials)
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        if (empty($authHeader)) {
            return false;
        };
        return true;
    }
    public static function extractFields($fieldsObj, $validFieldsAndMethods)
    {
        // fieldsObj format is [field => fieldValue]
        $fieldsToUpdate = [];
        $data = [];

        foreach ($validFieldsAndMethods as $field => $method) {
            if (isset($fieldsObj[$field])) {
                $fieldsToUpdate[] = "$field = :$field";
                $data[$field] = $method ? Utilities::$method($fieldsObj[$field]) : $fieldsObj[$field];
            }
        }
        return ["fields" => $fieldsToUpdate, "data" => $data];
    }
}
