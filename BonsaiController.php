<?php

function sanitize($input)
{
    if ($input) {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input);
    } else {
        $input = '';
    }
    return $input;
}

class BonsaiController
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function addBonsai()
    {
        $species = sanitize($_POST['species']);
        $origin_story = sanitize($_POST['origin_story']);
        $geolocation = sanitize($_POST['geolocation']);
        $photo_url = $_POST['photo_url'];

        try {
            // Prepare SQL statement
            $stmt = $this->db->prepare("INSERT INTO bonsais (species, origin_story, geolocation, photo_url) 
            VALUES (:species, :origin_story, :geolocation, :photo_url)");
            $stmt->bindParam(':species', $species);
            $stmt->bindParam(':origin_story', $origin_story);
            $stmt->bindParam(':geolocation', $geolocation);
            $stmt->bindParam(':photo_url', $photo_url);

            $stmt->execute();

            $lastid = $this->db->lastInsertId();
            http_response_code(201);
            echo json_encode(["message" => "$species bonsai id# $lastid added successfully"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function fetchAllBonsai()
    {

        try {
            $stmt = $this->db->prepare("SELECT * FROM bonsais");
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetchAll();
            http_response_code(200);
            echo json_encode(($result));
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}
