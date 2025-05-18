<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header("Content-Type: application/json");

require_once '../db_connect.php';

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "資料庫連線失敗: " . $conn->connect_error]);
    exit;
}

// SQL 查詢工作坊資料
$sql = "SELECT id, topic, date, lecturerName, lecturerTitle, maxAmount, currentAmount FROM workshop";
$result = $conn->query($sql);

$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(["success" => true, "data" => $data], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(["success" => true, "data" => []]);  // 沒資料也要成功
}

$conn->close();
?>
