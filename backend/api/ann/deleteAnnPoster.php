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

$aId = isset($_GET['aId']) ? intval($_GET['aId']) : null;
$posterUrl= isset($_GET['posterUrl']) ? $_GET['posterUrl'] : null;

if (!$aId || !$posterUrl) {
    echo json_encode(["success" => false, "error" => "缺少必要參數"]);
    exit;
}

// 刪除對應資料
$sql = "DELETE FROM ann_posterurl WHERE aId = ? AND posterUrl = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $aId, $posterUrl);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "找不到對應資料"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "刪除失敗"]);
}

$stmt->close();
$conn->close();
?>