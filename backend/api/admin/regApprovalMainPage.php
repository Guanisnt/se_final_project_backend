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
    "page" => 1,
    "totalPage" => 10,
    "data" => [
        [
            "teamId" => "2025team123",
            "teamName" => "對對隊",
            "workName" => "作品名稱",
            "state" => "已審核"
        ],
        [
            "teamId" => "2025team321",
            "teamName" => "不太隊",
            "workName" => "作品名稱",
            "state" => "需補件"
        ]
    ],
]);


/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */
?>