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

$workId = isset($_GET['workId']) ? $_GET['workId'] : null;

echo json_encode([
    "success" => true,
    "teamType" => "創意發想組",
    "teamName" => "對對隊",
    "member" => [
        [
            "name" => "陳怡君",
            "department" => "資訊工程系",
        ],
        [
            "name" => "李小明",
            "department" => "資訊工程系",
        ],
    ],
    "advisor" => [
        "name" => "王老師",
        "department" => "資訊工程系",
        "organization" => "高雄大學",
        "title" => "副教授",
    ],
    "workName" => "作品名稱",
    "workAbstract" => "作品摘要",
    "urls" => [
        "youtube.com",
        "github.com",
    ],
    "sdgs" => "1,2,3",
]);


/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */

$conn->close();
?>
