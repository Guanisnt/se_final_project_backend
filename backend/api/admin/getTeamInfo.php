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

$teamId = isset($_GET['teamId']) ? $_GET['teamId'] : null;

// 模擬回傳一筆公告資料
echo json_encode([
    "success" => true,
    "totalMembers" => 2,
    "teamInfo" => [
        [
            "uId" => "A1115566",
            "name" => "陳小明",
            "department" => "資訊工程系",
            "grade" => "大一",
            "email" => "A1115566@gmail.com",
            "phone" => "0912345678",
            "studentCard" => "學生證路徑",
        ],
        [
            "uId" => "A1115567",
            "name" => "李小華",
            "department" => "資訊工程系",
            "grade" => "大一",
            "email" => "A1115567@gmail.com",
            "phone" => "0987654321",
            "studentCard" => "學生證路徑",
        ]
    ],
    "workInfo" => [
        "workId" => "2025work001",
        "workName" => "衝衝衝",
        "poster" => "海報路徑",
        "urls" => [
            "https://example.com/work1",
            "https://example.com/work2"
        ],
        "workAbstract" => "作品摘要",
        "affidavit" => "切結書路徑",
        "consent" => "同意書路徑",
        "introduction" => "說明書路徑",
        "sdgs" => "1,2,3",
        "state" => "待審核",
    ]
]);


/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */

$conn->close();
?>
