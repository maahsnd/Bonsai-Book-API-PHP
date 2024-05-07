<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

function sanitize($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

// Create bonsai
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input
    $species = sanitize($_POST['species']);
    $origin_story = sanitize($_POST['origin_story']);
    $geolocation = sanitize($_POST['geolocation']);
    $photo_url = $_POST['photo_url']; // Ensure this is named correctly as in your form


    // Set up SQLite connection
    $db = new PDO('sqlite:bonsai_book.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        // Prepare SQL statement
        $stmt = $db->prepare("INSERT INTO bonsais (species, origin_story, geolocation, photo_url) VALUES (:species, :origin_story, :geolocation, :photo_url)");
        $stmt->bindParam(':species', $species);
        $stmt->bindParam(':origin_story', $origin_story);
        $stmt->bindParam(':geolocation', $geolocation);
        $stmt->bindParam(':photo_url', $photo_url);

        $stmt->execute();

        $lastid = $db->lastInsertId();
        echo "$species bonsai id# $lastid added successfully";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
