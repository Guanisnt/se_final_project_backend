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

// 驗證必要參數
if (!isset($input['workshopTopic']) || !isset($input['maxAmount']) || 
    !isset($input['lecturerId']) || !isset($input['adminId']) || 
    !isset($input['datetime'])) {
    echo json_encode(["success" => false, "error" => "缺少必要參數"]);
    exit;
}

$workshopTopic = $input['workshopTopic'];
$maxAmount = (int)$input['maxAmount'];
$lecturerId = $input['lecturerId'];
$adminId = $input['adminId'];
$datetime = $input['datetime'];

// 驗證參數格式
if (empty($workshopTopic) || $maxAmount <= 0 || empty($lecturerId) || 
    empty($adminId) || empty($datetime)) {
    echo json_encode(["success" => false, "error" => "參數格式不正確"]);
    exit;
}

// 驗證日期時間格式
$dateTimeObj = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
if (!$dateTimeObj || $dateTimeObj->format('Y-m-d H:i:s') !== $datetime) {
    echo json_encode(["success" => false, "error" => "日期時間格式不正確，請使用 YYYY-MM-DD HH:MM:SS 格式"]);
    exit;
}

// 插入工作坊資料
$sql = "INSERT INTO workshop (time, topic, maxAmount, currentAmount, adminId, lecturerId) 
        VALUES (?, ?, ?, 0, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "SQL預備失敗: " . $conn->error]);
    exit;
}

$stmt->bind_param("ssiss", $datetime, $workshopTopic, $maxAmount, $adminId, $lecturerId);

if ($stmt->execute()) {
    $newWorkshopId = $conn->insert_id;
    echo json_encode([
        "success" => true, 
        "message" => "工作坊新增成功",
        "workshopId" => $newWorkshopId
    ]);
} else {
    echo json_encode(["success" => false, "error" => "新增失敗: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
