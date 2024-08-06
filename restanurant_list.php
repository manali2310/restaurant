<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$conn = new mysqli("localhost", "root", "", "restaurant_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $result = $conn->query("SELECT * FROM restaurants");
        echo json_encode($result->fetch_all(MYSQLI_ASSOC));
        break;

    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("INSERT INTO restaurants (name, description, location) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $input['name'], $input['description'], $input['location']);
        $stmt->execute();
        echo json_encode(["id" => $conn->insert_id]);
        break;

    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("UPDATE restaurants SET name=?, description=?, location=? WHERE id=?");
        $stmt->bind_param("sssi", $input['name'], $input['description'], $input['location'], $input['id']);
        $stmt->execute();
        echo json_encode(["message" => "Record updated"]);
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents("php://input"), true);
        if (isset($input['id'])) {
            $id = $input['id'];
            $stmt = $conn->prepare("DELETE FROM restaurants WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            echo json_encode(["message" => "Record deleted"]);
        } else {
            echo json_encode(["error" => "Invalid request: 'id' parameter is missing"]);
        }
        break;

    default:
        echo json_encode(["message" => "Invalid request"]);
        break;
}

$conn->close();
?>
