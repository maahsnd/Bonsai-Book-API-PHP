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
            $sql = "INSERT INTO users (username, bio, geolocation,photo_url) 
            VALUES (:username, :bio, :geolocation, :photo_url)";
            $stmt = $this->db->prepare($sql);
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
            $sql = "SELECT * FROM users WHERE id=$id";
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

    public function updateUser($id)
    {
        //Test after user edits implemented

/*         $headers = getallheaders();
        if(in_array('Authorization', $headers)) {
            $authToken = $headers['Authorization'];
            $valid = Utilities::validateUser($authToken, $id);
            if ($valid == false) {
                http_response_code(401);
                return;
            }
        } */

        if ('PUT' === $_SERVER['REQUEST_METHOD']) {
            parse_str(file_get_contents('php://input'), $_PUT);
        } else {
            http_response_code(405);
            return;
        }

        $fields = [
            'username' => 'sanitize',
            'bio' => 'sanitize',
            'geolocation' => 'sanitize',
            'photo_url' => 'trimAndEscape'
        ];

        ["fields" => $fieldsToUpdate, "data" => $data] = Utilities::extractFields($_PUT, $fields);

        

        if (empty($fieldsToUpdate)) {
            http_response_code(400);
            echo json_encode(["error" => "No data included to update"]);
            return;
        }

        $sql = "UPDATE users SET " . implode(', ', $fieldsToUpdate) . " WHERE id = :id";
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
}
