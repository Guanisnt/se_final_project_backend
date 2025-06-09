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

if (!isset($input['score'], $input['judgeId'], $input['workId'])) {
    echo json_encode(["success" => false, "error" => "缺少必要欄位"]);
    exit;
}

$score = $input['score'];
$judgeId = $input['judgeId'];
$workId = $input['workId'];

// 先檢查是否已經有評分紀錄
$check_sql = "SELECT COUNT(*) AS cnt FROM score WHERE uId = ? AND wId = ?";
$stmt_check = $conn->prepare($check_sql);
$stmt_check->bind_param("ss", $judgeId, $workId);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$row_check = $result_check->fetch_assoc();
$count = intval($row_check['cnt']);
$stmt_check->close();

if ($count > 0) {
    // 已經評分過，執行更新
    $update_sql = "UPDATE score SET score = ? WHERE uId = ? AND wId = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("dss", $score, $judgeId, $workId);
    $success = $stmt_update->execute();
    $stmt_update->close();
} else {
    // 沒有評分過，執行新增
    $insert_sql = "INSERT INTO score (score, uId, wId) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($insert_sql);
    $stmt_insert->bind_param("dss", $score, $judgeId, $workId);
    $success = $stmt_insert->execute();
    $stmt_insert->close();
}

if ($success) {
    echo json_encode([
        "success" => true,
        "score" => $score
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "資料庫操作失敗"
    ]);
}

$conn->close();
?>
