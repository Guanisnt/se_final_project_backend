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

// // 模擬回傳一筆公告資料
// echo json_encode([
//     "success" => true,
//     "page" => 1,
//     "totalPage" => 10,
//     "data" => [
//         [
//             "teamId" => "2025team123",
//             "teamName" => "對對隊",
//             "workIntroduction" => "作品說明書檔案路徑",
//             "workConsent" => "個資同意書檔案路徑",
//             "workAffidavit" => "提案切結書檔案路徑",
//             "state" => "已審核"
//         ],
//         [
//             "teamId" => "2025team321",
//             "teamName" => "不太隊",
//             "workIntroduction" => "",
//             "workConsent" => "個資同意書檔案路徑",
//             "workAffidavit" => "",
//             "state" => "需補件"
//         ]
//     ],
// ]);
$year = isset($_GET['year']) ? $_GET['year'] : null;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

if (!$year || $page < 1) {
    echo json_encode(["success" => false, "error" => "缺少必要參數或 page 無效"]);
    exit;
}

$limit = 10;
$offset = ($page - 1) * $limit;

// 取得總筆數
$total_sql = "
    SELECT COUNT(*) AS total
    FROM team
    WHERE tId LIKE CONCAT(?, 'team%')
";
$stmt_total = $conn->prepare($total_sql);
$stmt_total->bind_param("s", $year);
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_row = $total_result->fetch_assoc();
$total = intval($total_row['total']);
$stmt_total->close();

$totalPages = ceil($total / $limit);

// 是否有下一頁
$hasNext = $page < $totalPages;

// 查詢該頁資料，join work 取得作品名稱
$sql = "
    SELECT t.tId AS teamId, t.name AS teamName, w.name AS workName, w.state
    FROM team t
    LEFT JOIN work w ON t.tId = w.tId
    WHERE t.tId LIKE CONCAT(?, 'team%')
    ORDER BY t.tId ASC
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $year, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// 無論有沒有資料，都回傳成功（空陣列即可）
echo json_encode([
    "success" => true,
    "page" => $page,
    "totalPage" => $totalPages,
    "data" => $data
]);

$stmt->close();
$conn->close();
?>

