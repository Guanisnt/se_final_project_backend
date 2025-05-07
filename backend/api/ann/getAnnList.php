<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header("Content-Type: application/json");
#$input = json_decode(file_get_contents("php://input"), true);
require_once '../db_connect.php';

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "資料庫連線失敗"]);
    exit;
}

// $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
// echo json_encode([
//     "success" => true,
//     "message" => "API 有動",
//     "page" => $page
// ]);

/* === 從這邊以下開始寫資料庫操作 === */

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10; // 每頁顯示的公告數量
$offset = ($page - 1) * $limit; // 開始顯示的公告位置(由0開始算)
//offset = 0, limit=10 => 0~9筆

// 查詢總公告數量
$total_sql = "SELECT COUNT(*) AS total FROM ann";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total = intval($total_row['total']);

$totalPages = ceil($total / $limit); //無條件進位
$hasNext = $page < $totalPages; // 是否有下一頁

// 查詢該頁公告
$sql = "SELECT aId, title, DATE(time) AS publishDate 
        FROM ann 
        ORDER BY time DESC 
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$data = []; // 用來存放查詢公告資訊結果的陣列
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

if ($page <= $totalPages) {
    echo json_encode([
        "success" => true,
        "page" => $page,
        "hasNext" => $hasNext,
        "totalPages" => $totalPages,
        "data" => $data
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "公告不夠多到下一頁"
    ]);
}

$stmt->close(); // 關閉公告查詢的預處理語句
$conn->close();
?>