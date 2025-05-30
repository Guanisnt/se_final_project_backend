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

$uId = isset($_GET['uId']) ? $_GET['uId'] : null;

echo json_encode([
    "success" => true,
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
]);


/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */

$conn->close();
?>
