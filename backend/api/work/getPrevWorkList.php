<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header("Content-Type: application/json");
// $input = json_decode(file_get_contents("php://input"), true);
require_once '../db_connect.php';

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "資料庫連線失敗"]);
    exit;
}

// echo json_encode([
//     "success" => true,
//     "data" => [
//         [
//             "workId" => 1,
//             "teamRank" => 1,
//             "teamName" => "Team A",
//             "workName" => "Work A",
//             "advisor" => "指導教授名字",
//         ],
//         [
//             "workId" => 2,
//             "teamRank" => 2,
//             "teamName" => "Team B",
//             "workName" => "Work B",
//             "advisor" => "指導教授名字",
//         ],
//     ]
// ]);

/* === 從這邊以下開始寫資料庫操作 === */

// 取得前端傳來的資料
$input = json_decode(file_get_contents("php://input"), true);
$year = isset($input['year']) ? $input['year'] : null;
$teamType = isset($input['teamType']) ? $input['teamType'] : null;

if (!$year || !$teamType) {
    echo json_encode(["success" => false, "error" => "缺少必要參數"]);
    exit;
}

// 預備 SQL 查詢
$sql = "SELECT w.wId, t.rank, t.name as teamName, w.name as workName, 
               CONCAT(u.name, ' ', IFNULL(te.title, '')) as advisor
        FROM work w
        JOIN team t ON w.tId = t.tId
        LEFT JOIN users u ON t.uId = u.uId
        LEFT JOIN teacher te ON t.uId = te.uId
        WHERE w.wId LIKE ? AND t.type = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "SQL預備失敗: " . $conn->error]);
    exit;
}
$wIdPrefix = $year . '%';
$stmt->bind_param("ss", $wIdPrefix, $teamType);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "error" => "查詢失敗: " . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "workId" => $row["wId"],
        "teamRank" => $row["rank"],
        "teamName" => $row["teamName"],
        "workName" => $row["workName"],
        "advisor" => $row["advisor"]
    ];
}

echo json_encode([
    "success" => true,
    "data" => $data
]);

$stmt->close();
$conn->close();
?>
