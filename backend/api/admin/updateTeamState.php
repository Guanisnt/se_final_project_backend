<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);
require_once '../db_connect.php';

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "資料庫連線失敗"]);
    exit;
}

$teamId = $input['teamId'] ?? null;
$state = $input['state'] ?? null;
$message = $input['message'] ?? null;

if (!$teamId || !$state) {
    echo json_encode([
        "success" => false,
        "error" => "缺少必要參數"
    ]);
    exit;
}

// 如果狀態不是"需補件"，將message設為null
if ($state !== "需補件") {
    $message = null;
}

$sql = "UPDATE work SET state = ?, message = ? WHERE tId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $state, $message, $teamId);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "更新失敗"
    ]);
}

$stmt->close();
$conn->close();
?>
