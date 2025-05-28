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

echo json_encode([
    "success" => true,
    // "aId" => $aId,
    // "posterUrl" => $posterUrl,
]);
/* === 從這邊以下開始寫資料庫操作 === */
?>