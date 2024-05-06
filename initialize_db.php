<?php
date_default_timezone_set('America/Los_Angeles');

try {
    // Create (connect to) SQLite database in file
    $db = new PDO('sqlite:bonsai_book.db');
    // Set errormode to exceptions
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->exec("CREATE TABLE IF NOT EXISTS bonsais (
        id INTEGER PRIMARY KEY AUTOINCREMENT, 
        species VARCHAR(75) NOT NULL,
        dob VARCHAR(75),
        geolocation VARCHAR(255) NOT NULL,
        photo_url_arr TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    echo "Database and bonsais table created successfully.";
} catch (PDOException $e) {
    echo "An error occurred: " . $e->getMessage();
}
