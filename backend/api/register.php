<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);
$id = $input["id"];
$username = $input["username"];

$conn = new mysqli("localhost", "root", "12345678", "dbfinalproject");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "資料庫連線失敗"]);
    exit;
}

// 準備插入資料
$stmt = $conn->prepare("INSERT INTO users (id, name) VALUES (?, ?)");

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "SQL錯誤：" . $conn->error]);
    exit;
}

// 注意：如果id是數字(int)，用 "i"，如果是文字(string)，用 "s"
$stmt->bind_param("is", $id, $username);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
