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

echo json_encode([
    "success" => true,
    "teamInfo" => [
        "workSate" => "待上傳",
        "teamName" => "對對隊",
        "teamType" => "創意發想組",
        "workName" => "作品名稱",
        "workAbstract" => "作品摘要",
        "workUrls" => [
            "https://example.com/work1",
            "https://example.com/work2"
        ],
        "sdgs" => "1, 2",
        "workIntroduction" => "作品說明書檔案路徑",
        "workConsent" => "個資同意書檔案路徑",
        "workAffidavit" => "提案切結書檔案路徑",
    ],
    "advisorInfo" => [
        "name" => "陳老師",
        "title" => "指導老師",
        "department" => "資訊工程系",
        "organization" => "高雄大學",
    ],
    "memberInfo" => [
        [
            "uId" => "a1115555",
            "name" => "陳小明",
            "department" => "資訊工程系",
            "grade" => "大一",
            "email" => "a1115555@gmail.com",
            "phone" => "0912345678",
            "studentCard" => "student_card_path_1.jpg"
        ],
        [
            "uId" => "a1115566",
            "name" => "張三",
            "department" => "資訊工程系",
            "grade" => "大一",
            "email" => "a1115566@gmail.com",
            "phone" => "0912345678",
            "studentCard" => "student_card_path_2.jpg"
        ],
    ],
]);


/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */

$conn->close();
?>
