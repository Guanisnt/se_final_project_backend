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

// 新增分數資料
$sql = "INSERT INTO score (score, uId, wId) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $score, $judgeId, $workId);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "score" => $score
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "資料庫新增失敗"
    ]);
}

$stmt->close();
$conn->close();
?>

