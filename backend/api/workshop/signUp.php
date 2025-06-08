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

$uId = $input['uId'] ?? '';
$workshopId = $input['workshopId'] ?? '';

if (empty($uId) || empty($workshopId)) {
    echo json_encode(["success" => false, "error" => "缺少參數"]);
    exit;
}

// 看人數是不是滿了
$checkSql = "SELECT currentAmount, maxAmount FROM workshop WHERE wsId = ?";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param("i", $workshopId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "error" => "找不到對應的工作坊"]);
    exit;
}

$row = $result->fetch_assoc();
$current = (int)$row['currentAmount'];
$max = (int)$row['maxAmount'];

if ($current >= $max) {
    echo json_encode(["success" => false, "error" => "人數已滿，無法報名"]);
    exit;
}

$stmt->close();
// 開始交易（確保兩個動作都成功才提交）
$conn->begin_transaction();

try {
    // 插入報名紀錄
    $insertSql = "INSERT INTO attend (uId, wsId) VALUES (?, ?)";
    $stmt1 = $conn->prepare($insertSql);
    $stmt1->bind_param("si", $uId, $workshopId);
    $stmt1->execute();

    // 更新 workshop currentAmount +1
    $updateSql = "UPDATE workshop SET currentAmount = currentAmount + 1 WHERE wsId = ?";
    $stmt2 = $conn->prepare($updateSql);
    $stmt2->bind_param("i", $workshopId);
    $stmt2->execute();

    // 兩步都成功，提交交易
    $conn->commit();

    echo json_encode(["success" => true]);

} catch (Exception $e) {
    $conn->rollback(); // 回滾交易
    echo json_encode(["success" => false, "error" => "報名失敗: " . $e->getMessage()]);
}

$stmt1->close();
$stmt2->close();
$conn->close();

?>