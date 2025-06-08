<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header("Content-Type: application/json");
require_once '../db_connect.php';

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "資料庫連線失敗"]);
    exit;
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$itemsPerPage = 5;
$offset = ($page - 1) * $itemsPerPage;

$teacherId = isset($_GET['teacherId']) ? $_GET['teacherId'] : null;
if (empty($teacherId)) {
    echo json_encode(["success" => false, "error" => "請提供 teacherId"]);
    exit;
}

// 查詢老師指導的隊伍總數
$count_sql = "SELECT COUNT(*) AS total FROM team WHERE uId = ?";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("s", $teacherId);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_assoc();
$totalTeams = intval($count_row['total']);
$totalPages = ceil($totalTeams / $itemsPerPage);
$count_stmt->close();

// 查詢指導隊伍資料
$sql = "
    SELECT 
        t.tId AS teamId,
        t.name AS teamName,
        t.type AS teamType,
        t.uId AS teacherId,
        t.leader AS teamLeader,
        w.name AS workName
    FROM team t
    LEFT JOIN work w ON t.tId = w.tId
    WHERE t.uId = ?
    LIMIT ?, ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $teacherId, $offset, $itemsPerPage);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $teamId = $row['teamId'];
    $teamLeader = $row['teamLeader'];
    $attendYear = intval(substr($teamId, 0, 4));

    // 查詢隊伍成員
    $member_sql = "
        SELECT u.name, s.department, a.uId
        FROM attendee a
        JOIN users u ON a.uId = u.uId
        JOIN student s ON u.uId = s.uId
        WHERE a.tId = ?
    ";
    $member_stmt = $conn->prepare($member_sql);
    $member_stmt->bind_param("s", $teamId);
    $member_stmt->execute();
    $member_result = $member_stmt->get_result();

    $members = [];
    while ($member_row = $member_result->fetch_assoc()) {
        $members[] = [
            "name" => $member_row['name'],
            "department" => $member_row['department'],
            "uId" => $member_row['uId']
        ];
    }
    $member_stmt->close();

    // 隊長排第一
    usort($members, function ($a, $b) use ($teamLeader) {
        if ($a['uId'] === $teamLeader) return -1;
        if ($b['uId'] === $teamLeader) return 1;
        return 0;
    });

    // 去掉 uId，只留 name 跟 department
    $members = array_map(function ($m) {
        return [
            "name" => $m['name'],
            "department" => $m['department']
        ];
    }, $members);

    $data[] = [
        "teacherId" => $row['teacherId'],
        "attendYear" => $attendYear,
        "teamType" => $row['teamType'],
        "teamId" => $teamId,
        "teamName" => $row['teamName'],
        "workName" => $row['workName'],
        "members" => $members
    ];
}

echo json_encode([
    "success" => true,
    "page" => $page,
    "totalPage" => $totalPages,
    "data" => $data
], JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();
?>
