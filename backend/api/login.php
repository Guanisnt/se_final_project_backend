<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header("Content-Type: application/json");
$input = json_decode(file_get_contents("php://input"), true);

$conn = new mysqli("localhost", "root", "12345678", "se_final_project");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "資料庫連線失敗"]);
    exit;
}

$conn->close();
?>
