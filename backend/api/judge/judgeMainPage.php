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

$total_sql = "SELECT COUNT(*) AS total FROM team";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_count = intval($total_row['total']);
$totalPage = ceil($total_count / $limit);

// 4️⃣ 查詢該頁面資料
$sql = "
    SELECT 
        t.tId AS teamId,
        t.name AS teamName,
        t.type AS teamType,
        w.name AS workName
    FROM team t
    LEFT JOIN work w ON t.tId = w.tId
    ORDER BY t.tId ASC
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "teamType" => $row['teamType'],
        "teamName" => $row['teamName'],
        "workName" => $row['workName'],
        "teamId"   => $row['teamId']
    ];
}

// 5️⃣ 回傳 JSON
echo json_encode([
    "success" => true,
    "page" => $page,
    "totalPage" => $totalPage,
    "data" => $data
]);

$stmt->close();
$conn->close();
?>