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

// 取得 GET 參數
$userType = isset($_GET['userType']) ? $_GET['userType'] : null;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

if (!$userType) {
    echo json_encode(["success" => false, "error" => "缺少 userType 參數"]);
    exit;
}

$limit = 10;
$offset = ($page - 1) * $limit;

// 查詢總數量
$total_sql = "SELECT COUNT(*) AS total FROM users WHERE userType = ?";
$stmt_total = $conn->prepare($total_sql);
$stmt_total->bind_param("s", $userType);
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_row = $total_result->fetch_assoc();
$total = intval($total_row['total']);
$stmt_total->close();

$totalPages = ceil($total / $limit);

// 是否有下一頁
$hasNext = $page < $totalPages;

// 查詢該頁使用者
$sql = "SELECT uId, name FROM users WHERE userType = ? ORDER BY uId ASC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $userType, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

if ($page <= $totalPages) {
    echo json_encode([
        "success" => true,
        "page" => $page,
        "totalPage" => $totalPages,
        "data" => $data
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "無使用者資料或超出頁數"
    ]);
}

$stmt->close();
$conn->close();
?>
