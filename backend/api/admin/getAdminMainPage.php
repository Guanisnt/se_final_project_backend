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

// 模擬回傳一筆公告資料
echo json_encode([
    "success" => true,
    "userManage" => [
        "totalUser" => 1900,
        "stuAmount" => 1100,
        "attendeeAmount" => 200,
        "techerAmount" => 500,
        "lecturerAmount" => 100,
        "judgeAmount" => 200,
    ],
    "workState" => [
        "totalTeam" => 1900,
        "accepted" => 1100,
        "pending" => 120,
        "supplementary" => 500,
        "notUploadYet" => 80,
    ]
]);


/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */

$conn->close();
?>
