<?php
require_once 'utils.php';

class BonsaiController
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    private $validFields = [
        'species',
        'origin_story',
        'geolocation',
        'photo_url',
        'author'
    ];

    public function addBonsai()
    {
        $species = Utilities::sanitize($_POST['species']);
        $origin_story = Utilities::sanitize($_POST['origin_story']);
        $geolocation = Utilities::sanitize($_POST['geolocation']);
        $photo_url = Utilities::trimAndEsc($_POST['photo_url']);
        $author = Utilities::sanitize($_POST['author']);

        try {
            $sql = "INSERT INTO bonsais (species, origin_story, geolocation, author, photo_url) 
            VALUES (:species, :origin_story, :geolocation, :author, :photo_url)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':species', $species);
            $stmt->bindParam(':origin_story', $origin_story);
            $stmt->bindParam(':geolocation', $geolocation);
            $stmt->bindParam(':author', $author);
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

    public function searchBonsai($searchTerms = [])
    {
        try {
            $sql = "SELECT * FROM bonsais";
            $conditions = [];
            $params = [];

            if (!empty($searchTerms)) {
                // Validate and build conditions from search terms
                foreach ($searchTerms as $key => $value) {
                    if (in_array($key, $this->validFields)) {
                        $conditions[] = "$key = :$key";
                        $cleanVal = Utilities::sanitize($value);
                        //Data is stored with spaces, search terms are passed with underscores
                        $processedVal = str_replace('_', ' ', $cleanVal);
                        $params[$key] = $processedVal;
                    }
                }
                $sql .= " WHERE " . implode(' AND ', $conditions);
            } else {
                $sql .= " LIMIT 30";
            }

            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => &$value) {
                $stmt->bindParam(':' . $key, $value);
            }

            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($result)) {
                http_response_code(404);
            } else {
                http_response_code(200);
                echo json_encode($result);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }


    public function fetchOneBonsai($id)
    {
        try {
            $sql = "SELECT * FROM bonsais WHERE id=$id";
            $stmt = $this->db->prepare($sql);
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
            'author' => 'sanitize',
            'photo_url' => 'trimAndEsc'
        ];

        ["fields" => $fieldsToUpdate, "data" => $data] = Utilities::extractFields($_PUT, $fields);

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
            $sql = "DELETE FROM bonsais WHERE id=$id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            http_response_code(200);
        } catch (PDOException $e) {
            http_response_code(404);
            echo json_encode($e->getMessage());
        }
    }
}
