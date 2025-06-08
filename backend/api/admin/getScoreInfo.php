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

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$year = isset($_GET['year']) ? $_GET['year'] : null;
$teamType = isset($_GET['teamType']) ? $_GET['teamType'] : null;

if (!$year || !$teamType) {
    echo json_encode(["success" => false, "error" => "缺少必要參數"]);
    exit;
}

$limit = 10;
$offset = ($page - 1) * $limit;

// 查詢總作品數
$countSql = "
    SELECT COUNT(w.wId)
    FROM work w
    JOIN team t ON w.tId = t.tId
    WHERE LEFT(w.wId, 4) = ? AND t.type = ?
";
$stmt = $conn->prepare($countSql);
$stmt->bind_param("ss", $year, $teamType);
$stmt->execute();
$stmt->bind_result($totalRows);
$stmt->fetch();
$stmt->close();

$totalPages = ceil($totalRows / $limit);

// 查詢作品與隊伍資訊及平均分數（若無評分則為 -1）
$sql = "
    SELECT 
        t.type AS teamType,
        t.name AS teamName,
        w.name AS workName,
        IFNULL(ROUND(AVG(s.score), 2), -1) AS score
    FROM work w
    JOIN team t ON w.tId = t.tId
    LEFT JOIN score s ON w.wId = s.wId
    WHERE LEFT(w.wId, 4) = ? AND t.type = ?
    GROUP BY w.wId
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $year, $teamType, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
$stmt->close();

echo json_encode([
    "success" => true,
    "page" => $page,
    "totalPages" => $totalPages,
    "data" => $data
], JSON_UNESCAPED_UNICODE);
?>
