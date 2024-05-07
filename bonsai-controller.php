<?php
require_once 'utils.php';

class BonsaiController
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function addBonsai()
    {
        $species = Utilities::sanitize($_POST['species']);
        $origin_story = Utilities::sanitize($_POST['origin_story']);
        $geolocation = Utilities::sanitize($_POST['geolocation']);
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
            return $lastid;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function fetchAllBonsai()
    {

        try {
            $stmt = $this->db->prepare("SELECT * FROM bonsais");
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetchAll();
            return $result;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    public function fetchOneBonsai($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM bonsais WHERE id=$id");
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetchAll();
            return $result;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
}