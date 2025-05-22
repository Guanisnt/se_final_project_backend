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

if (!$uId) {
    echo json_encode([
        "success" => false,
        "error" => "缺少 uId 參數"
    ]);
    exit;
}

// 模擬回傳一筆公告資料
// echo json_encode([
//     "success" => true,
//     "workshopId" => [1, 2, 3]
// ]);


/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */
$sql = "SELECT wsId FROM attend WHERE uId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $uId);
$stmt->execute();
$result = $stmt->get_result();

$workshopId = [];
while ($row = $result->fetch_assoc()) {
    $workshopId[] = (int) $row['wsId']; // 明確轉為 int，確保前端型別一致
}

if (!empty($workshopId)) {
    echo json_encode([
        "success" => true,
        "workshopId" => $workshopId
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "無報名任何工作坊"
    ]);
}

$stmt->close();
$conn->close();
?>
