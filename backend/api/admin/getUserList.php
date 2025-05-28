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

$userType = isset($_GET['userType']) ? $_GET['userType'] : null;

// 模擬回傳一筆公告資料
echo json_encode([
    "success" => true,
    "page" => 1,
    "totalPage" => 10,
    "data" => [
        [
            "userId" => "a1115555",
            "name" => "陳小明",
        ],
        [
            "userId" => "a1115556",
            "name" => "陳小華",
        ],
    ],
    // "userType" => $userType,
]);


/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */

$conn->close();
?>
