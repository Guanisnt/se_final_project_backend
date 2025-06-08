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

try {
    // 直接從 users 表取得總人數
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $stmt->bind_result($totalUser);
    $stmt->fetch();
    $stmt->close();

    // 各角色數量
    $stmt = $conn->prepare("SELECT COUNT(*) FROM student");
    $stmt->execute();
    $stmt->bind_result($stuAmount);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM attendee");
    $stmt->execute();
    $stmt->bind_result($attendeeAmount);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM teacher");
    $stmt->execute();
    $stmt->bind_result($teacherAmount);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM lecturer");
    $stmt->execute();
    $stmt->bind_result($lecturerAmount);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM judge");
    $stmt->execute();
    $stmt->bind_result($judgeAmount);
    $stmt->fetch();
    $stmt->close();

    // 查詢作品狀態數量
    $stmt = $conn->prepare("SELECT COUNT(*) FROM team");
    $stmt->execute();
    $stmt->bind_result($totalTeam);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM work WHERE state = '已審核'");
    $stmt->execute();
    $stmt->bind_result($accepted);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM work WHERE state = '待審核'");
    $stmt->execute();
    $stmt->bind_result($pending);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM work WHERE state = '需補件'");
    $stmt->execute();
    $stmt->bind_result($supplementary);
    $stmt->fetch();
    $stmt->close();

    echo json_encode([
        "success" => true,
        "userManage" => [
            "totalUser" => $totalUser,
            "stuAmount" => $stuAmount,
            "attendeeAmount" => $attendeeAmount,
            "techerAmount" => $teacherAmount,
            "lecturerAmount" => $lecturerAmount,
            "judgeAmount" => $judgeAmount
        ],
        "workState" => [
            "totalTeam" => $totalTeam,
            "accepted" => $accepted,
            "pending" => $pending,
            "supplementary" => $supplementary,
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => "查詢失敗: " . $e->getMessage()]);
}

$conn->close();
?>
