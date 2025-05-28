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

// echo json_encode([
//     "success" => true,
//     "aId" => "回傳新增的公告的aId(因為後續新增poster或是file會用到)",
// ])
/* === 從這邊以下開始寫資料庫操作 === */

// 檢查必要欄位
if (!isset($input['annTitle'], $input['annContent'], $input['publishDate'], $input['uId'])) {
    echo json_encode(["success" => false, "error" => "缺少必要欄位"]);
    exit;
}

$annTitle = $input['annTitle'];
$annContent = $input['annContent'];
$publishDate = $input['publishDate'];
$uId = $input['uId'];

// 插入資料（不含 aId）
$insertSql = "INSERT INTO ann (title, content, time, uId) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($insertSql);
$stmt->bind_param("ssss", $annTitle, $annContent, $publishDate, $uId);

if ($stmt->execute()) {
    $newAnnId = $conn->insert_id; // 取得自動產生的 aId
    echo json_encode(["success" => true, "aId" => $newAnnId]);
} else {
    echo json_encode(["success" => false, "error" => "新增公告失敗"]);
}
?>
?>