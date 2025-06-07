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

$score = isset($input['score']) ? $input['score'] : null;
$workId = isset($input['workId']) ? $input['workId'] : null;
$judgeId = isset($input['judgeId']) ? $input['judgeId'] : null;

// 模擬回傳一筆公告資料
echo json_encode([
    "success" => true,
    "score" => $score,
    "workId" => $workId,
    "judgeId" => $judgeId,
]);


/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */

$conn->close();
?>
