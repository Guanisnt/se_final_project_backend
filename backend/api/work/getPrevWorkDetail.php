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
$wId = isset($_GET['workId']) ? $_GET['workId'] : null;

if (!$wId) {
    echo json_encode([
        "success" => false,
        "error" => "缺少 wId 參數"
    ]);
    exit;
}

// 模擬回傳一筆公告資料
// -

/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */

$sql = "SELECT t.name as teamName, t.type as teamType, w.name as workName, w.abstract as workAbstract, w.sdgs, w.introduction as workIntro
        FROM work w
        JOIN team t ON w.tId = t.tId
        WHERE w.wId = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "SQL預備失敗: " . $conn->error]);
    exit;
}
$stmt->bind_param("s", $wId);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "error" => "查詢失敗: " . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    // 查詢 work_url
    $urlSql = "SELECT url FROM work_url WHERE wId = ?";
    $urlStmt = $conn->prepare($urlSql);
    $urls = [];
    if ($urlStmt) {
        $urlStmt->bind_param("s", $wId);
        if ($urlStmt->execute()) {
            $urlResult = $urlStmt->get_result();
            while ($urlRow = $urlResult->fetch_assoc()) {
                $urls[] = $urlRow["url"];
            }
        }
        $urlStmt->close();
    }

    echo json_encode([
        "success" => true,
        "data" => [
            "teamName" => $row["teamName"],
            "teamType" => $row["teamType"],
            "workName" => $row["workName"],
            "workAbstract" => $row["workAbstract"],
            "sdgs" => $row["sdgs"],
            "workIntro" => $row["workIntro"],
            "workUrls" => $urls
        ]
    ]);
} else {
    echo json_encode([
        "success" => true,
        "data" => null
    ]);
}

$stmt->close();
$conn->close();
?>