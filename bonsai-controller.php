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
            http_response_code(200);
            echo json_encode(["lastid" => $lastid]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode($e->getMessage());
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
            echo json_encode($result);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode($e->getMessage());
        }
    }

    public function fetchOneBonsai($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM bonsais WHERE id=$id");
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
    public function updateBonsai($id)
    {
        if ('PUT' === $_SERVER['REQUEST_METHOD']) {
            parse_str(file_get_contents('php://input'), $_PUT);
        } else {
            http_response_code(405);
            return;
        }

        $fields = [
            'species' => 'sanitize',
            'origin_story' => 'sanitize',
            'geolocation' => 'sanitize',
            'photo_url' => null
        ];

        $fieldsToUpdate = [];
        $data = [];

        foreach ($fields as $field => $method) {
            if (isset($_PUT[$field])) {
                $fieldsToUpdate[] = "$field = :$field";
                $data[$field] = $method ? Utilities::$method($_PUT[$field]) : $_PUT[$field];
            }
        }

        if (empty($fieldsToUpdate)) {
            http_response_code(400);
            echo json_encode(["error" => "No data included to update"]);
            return;
        }

        $sql = "UPDATE bonsais SET " . implode(', ', $fieldsToUpdate) . " WHERE id = :id";
        $data['id'] = $id;

        try {
            $stmt = $this->db->prepare($sql);
            foreach ($data as $key => &$value) {
                $stmt->bindParam(':' . $key, $value);
            }
            $stmt->execute();
            $rowCount = $stmt->rowCount();
            http_response_code(200);
            echo json_encode(["rows_updated" => $rowCount]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode($e->getMessage());
        }
    }

    public function deleteBonsai($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM bonsais WHERE id=$id");
            $stmt->execute();
            http_response_code(200);
        } catch (PDOException $e) {
            http_response_code(404);
            echo json_encode($e->getMessage());
        }
    }
}
