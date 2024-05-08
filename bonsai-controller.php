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
    public function updateBonsai($id)
    {
        $fieldsToUpdate = [];
        $data = [];

        if (isset($_POST['species'])) {
            $fieldsToUpdate[] = "species = :species";
            $data['species'] = Utilities::sanitize($_POST['species']);
        }
        if (isset($_POST['origin_story'])) {
            $fieldsToUpdate[] = "origin_story = :origin_story";
            $data['origin_story'] = Utilities::sanitize($_POST['origin_story']);
        }
        if (isset($_POST['geolocation'])) {
            $fieldsToUpdate[] = "geolocation = :geolocation";
            $data['geolocation'] = Utilities::sanitize($_POST['geolocation']);
        }
        if (isset($_POST['photo_url'])) {
            $fieldsToUpdate[] = "photo_url = :photo_url";
            $data['photo_url'] = $_POST['photo_url'];
        }

        if (empty($fieldsToUpdate)) {
            http_response_code(400);
            echo json_encode(["error" => "No data included to update"]);
        }

        $sql = "UPDATE bonsais SET " . implode(', ', $fieldsToUpdate) . " WHERE id = :id";
        $data['id'] = $id;

        try {
            $stmt = $this->db->prepare($sql);
            foreach ($data as $key => &$value) {
                $stmt->bindParam(':' . $key, $value);
            }
            $result = $stmt->execute();

            return $result;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
}
