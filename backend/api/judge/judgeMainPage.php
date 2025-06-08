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

$page = isset($_GET['page']) ? $_GET['page'] : null;

// 模擬回傳一筆公告資料
echo json_encode([
    "success" => true,
    "page" => 1,
    "totalPage" => 10,
    "data" => [
        [
            "teamType" => "創意發想組",
            "teamName" => "對對隊",
            "workName" => "作品名稱",
        ],
        [
            "teamType" => "創意發想組",
            "teamName" => "創意隊",
            "workName" => "創意作品",
        ],
    ],
]);


/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */

$conn->close();
?>