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
$wId = isset($_GET['workId']) ? intval($_GET['workId']) : null;

if (!$wId) {
    echo json_encode([
        "success" => false,
        "error" => "缺少 wId 參數"
    ]);
    exit;
}

// 模擬回傳一筆公告資料
echo json_encode([
    "success" => true,
    "data" => [
        "teamName" => "隊伍名稱",
        "teamType" => "參賽組別",
        "workName" => "作品名稱",
        "workAbstract" => "作品摘要",
        "sdgs" => "一連串數字 e.g. 1,3,14",
        "workIntro" => "作品說明書檔案路徑",
        "workUrls" => [
            "youtube連結",
            "github連結",
        ],
    ]
]);


/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */

$conn->close();
?>
