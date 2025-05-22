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
$posterUrl= isset($_GET['fileUrl']) ? $_GET['fileUrl'] : null;

echo json_encode([
    "success" => true,
    // "aId" => $aId,
    // "fileName" => $fileName,
    // "fileUrl" => $posterUrl,
]);
/* === 從這邊以下開始寫資料庫操作 === */
?>