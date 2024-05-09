<?php
require_once 'utils.php';

class UserController
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function addUser()
    {
        $username = Utilities::sanitize($_POST['username']);
        $bio = Utilities::sanitize($_POST['bio']);
        $geolocation = Utilities::sanitize($_POST['geolocation']);
        $photo_url = Utilities::trimAndEsc($_POST['photo_url']);

        try {
            $stmt = $this->db->prepare("INSERT INTO users (username, bio, geolocation,photo_url) 
            VALUES (:username, :bio, :geolocation, :photo_url)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':bio', $bio);
            $stmt->bindParam(':geolocation', $geolocation);
            $stmt->bindParam(':photo_url', $photo_url);

            $stmt->execute();

            $lastid = $this->db->lastInsertId();
            http_response_code(200);
            echo json_encode(["lastid" => $lastid]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode($e->getMessage());
        }
    }

    public function getUser($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id=$id");
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetchAll();
            http_response_code(200);
            echo json_encode($result);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode($e->getMessage());
        }
    }
}
