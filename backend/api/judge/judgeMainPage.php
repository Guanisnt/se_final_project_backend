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

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;
$yearPrefix = date('Y'); // 例如 2025

// 計算總頁數：只計算「今年且有已審核作品」的隊伍
$total_sql = "
    SELECT COUNT(DISTINCT t.tId) AS total
    FROM team t
    LEFT JOIN work w ON t.tId = w.tId
    WHERE LEFT(t.tId, 4) = ? 
      AND LEFT(w.wId, 4) = ? 
      AND w.state = '已審核'
";
$stmt_total = $conn->prepare($total_sql);
$stmt_total->bind_param("ss", $yearPrefix, $yearPrefix);
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_row = $total_result->fetch_assoc();
$total_count = intval($total_row['total']);
$totalPage = ceil($total_count / $limit);
$stmt_total->close();


// 查詢符合條件隊伍及其「今年」作品（已審核），保留隊伍即使沒有作品
$sql = "
    SELECT 
        t.tId AS teamId,
        t.name AS teamName,
        t.type AS teamType,
        w.wId AS workId,
        w.name AS workName,
        IFNULL(ROUND(AVG(s.score), 2), -1) AS score
    FROM team t
    LEFT JOIN work w ON t.tId = w.tId
    LEFT JOIN score s ON w.wId = s.wId
    WHERE w.state = '已審核' AND LEFT(w.wId, 4) = ?
    GROUP BY t.tId, w.wId
    ORDER BY t.tId ASC
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $yearPrefix, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "teamType" => $row['teamType'],
        "teamName" => $row['teamName'],
        "workId"   => $row['workId'],
        "workName" => $row['workName'],
        "teamId"   => $row['teamId'],
        "score"    => $row['score']
    ];
}

echo json_encode([
    "success" => true,
    "page" => $page,
    "totalPage" => $totalPage,
    "data" => $data
]);

$stmt->close();
$conn->close();
?>
