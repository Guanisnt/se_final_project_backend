<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header("Content-Type: application/json");
require_once '../db_connect.php';

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "資料庫連線失敗"]);
    exit;
} 

$uId = isset($_GET['uId']) ? $_GET['uId'] : null;

if (!$uId) {
    echo json_encode(["success" => false, "error" => "缺少 uId 參數"]);
    exit;
}

// 更新 attendee 資料表，將 studentCard 設為 NULL
$sql = "UPDATE attendee SET studentCard = NULL WHERE uId = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "SQL預備失敗: " . $conn->error]);
    exit;
}

$stmt->bind_param("s", $uId);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
}

$stmt->close();
$conn->close();
?>