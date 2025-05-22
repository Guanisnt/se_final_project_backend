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
        "techerAmount" => 500,
        "lecturerAmount" => 100,
        "judgeAmount" => 200,
    ],
    "annManage" => [
        [
            "annTitle" => "公告１",
            "publishDate" => "2025-05-01",
        ],
        [
            "annTitle" => "公告2",
            "publishDate" => "2025-05-01",
        ],
        [
            "annTitle" => "公告3",
            "publishDate" => "2025-05-01",
        ],
    ],
    "workshopManage" => [
        [
            "workshopTopic" => "工作坊1",
            "workshopDate" => "2025-05-01",
        ],
        [
            "workshopTopic" => "工作坊2",
            "workshopDate" => "2025-05-01",
        ],
        [
            "workshopTopic" => "工作坊3",
            "workshopDate" => "2025-05-01",
        ],
    ]
]);


/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */

$conn->close();
?>
