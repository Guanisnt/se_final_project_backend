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

$workId = isset($_GET['workId']) ? $_GET['workId'] : null;

if (!$workId) {
    echo json_encode(["success" => false, "error" => "缺少 workId 參數"]);
    exit;
}

// 更新 work 資料表，將 introduction 設為 NULL
$sql = "UPDATE work SET introduction = NULL WHERE wId = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "SQL預備失敗: " . $conn->error]);
    exit;
}

$stmt->bind_param("s", $workId);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "success" => true,
            "message" => "作品說明書刪除成功"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "error" => "找不到對應的作品或作品說明書已不存在"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "error" => "刪除失敗: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>