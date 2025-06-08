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
$fileName= isset($_GET['fileName']) ? $_GET['fileName'] : null;
$fileUrl= isset($_GET['fileUrl']) ? urldecode($_GET['fileUrl']) : null;

if (!$aId || !$fileName || !$fileUrl) {
    echo json_encode(["success" => false, "error" => "缺少必要參數"]);
    exit;
}

// 刪除對應資料
$sql = "DELETE FROM ann_file WHERE aId = ? AND fileName = ? AND fileUrl = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $aId, $fileName, $fileUrl);

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