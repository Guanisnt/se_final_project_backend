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
//     // "input" => $input,
//     // "aId" => $input['aId'],
//     // "annTitle" => $input['annTitle'],
//     // "annContent" => $input['annContent'],
//     // "uId" => $input['uId'],
// ]);
/* === 從這邊以下開始寫資料庫操作 === */
$aId = isset($input['aId']) ? $input['aId'] : null;
$annTitle = isset($input['annTitle']) ? $input['annTitle'] : null;
$annContent = isset($input['annContent']) ? $input['annContent'] : null;
$publishDate = isset($input['publishDate']) ? $input['publishDate'] : null;
$uId = isset($input['uId']) ? $input['uId'] : null;

// 基本驗證
if (!$aId || !$annTitle || !$annContent || !$uId) {
    echo json_encode(["success" => false, "error" => "請完整提供公告資訊"]);
    exit;
}

// 更新公告
$sql = "UPDATE ann SET title = ?, content = ?, time = ?, uId = ? WHERE aId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $annTitle, $annContent, $publishDate, $uId, $aId);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "沒有修改任何資料"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "更新失敗"]);
}


$stmt->close();
?>
