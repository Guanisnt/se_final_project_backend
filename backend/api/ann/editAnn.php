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

echo json_encode([
    "success" => true,
    // "input" => $input,
    // "aId" => $input['aId'],
    // "annTitle" => $input['annTitle'],
    // "annContent" => $input['annContent'],
    // "uId" => $input['uId'],
]);
/* === 從這邊以下開始寫資料庫操作 === */
?>