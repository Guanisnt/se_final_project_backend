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

$uId = isset($_GET['uId']) ? $_GET['uId'] : null;

if (!$uId) {
    echo json_encode(["success" => false, "error" => "缺少 uId 參數"]);
    exit;
}

// 根據 uId 查詢對應使用者
$sql = "SELECT name FROM users WHERE uId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $uId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "success" => true,
        "name" => $row['name'],
        "uId" => $uId
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "查無使用者"
    ]);
}

$stmt->close();
$conn->close();
?>
