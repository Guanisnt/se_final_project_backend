<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);
require_once '../db_connect.php';

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "資料庫連線失敗"]);
    exit;
}

// 提取欄位
$teamName = $input['teamName'];
$teamType = $input['teamType'];
$workName = $input['workName'];
$workAbstract = $input['workAbstract'];
$workUrls = $input['workUrls'];
$sdgs = $input['sdgs'];
$numberOfMember = $input['numberOfMember'];
$teamMembers = $input['teamMembers'];
$teacherId = $input['teacherId'];

// 驗證學生是否存在且未加入其他隊伍
foreach ($teamMembers as $uId) {
    // 驗證學生存在
    $stmt = $conn->prepare("SELECT COUNT(*) FROM student WHERE uId = ?");
    $stmt->bind_param("s", $uId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count === 0) {
        echo json_encode(["success" => false, "error" => "學生帳號不存在：" . $uId]);
        exit;
    }

    // 驗證是否已參加其他隊伍
    $stmt = $conn->prepare("SELECT COUNT(*) FROM attendee WHERE uId = ?");
    $stmt->bind_param("s", $uId);
    $stmt->execute();
    $stmt->bind_result($exists);
    $stmt->fetch();
    $stmt->close();

    if ($exists > 0) {
        echo json_encode(["success" => false, "error" => "學生已加入其他隊伍：" . $uId]);
        exit;
    }
}


// 驗證老師
$stmt = $conn->prepare("SELECT COUNT(*) FROM teacher WHERE uId = ?");
$stmt->bind_param("s", $teacherId);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();
if ($count === 0) {
    echo json_encode(["success" => false, "error" => "教師帳號不存在：" . $teacherId]);
    exit;
}

// 生成 teamId
$year = date("Y");
$sql = "SELECT tId FROM team WHERE tId LIKE ? ORDER BY CAST(SUBSTRING(tId, ?) AS UNSIGNED) DESC LIMIT 1";
$like = $year . 'team%';
$prefixLength = strlen($year . 'team') + 1;
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $like, $prefixLength);
$stmt->execute();
$result = $stmt->get_result();
$maxNum = 0;
if ($row = $result->fetch_assoc()) {
    if (preg_match("/^{$year}team(\d+)$/", $row['tId'], $matches)) {
        $maxNum = intval($matches[1]);
    }
}
$newTeamId = $year . "team" . ($maxNum + 1);

// 生成 workId
$sql = "SELECT wId FROM work WHERE wId LIKE ? ORDER BY CAST(SUBSTRING(wId, ?) AS UNSIGNED) DESC LIMIT 1";
$like = $year . 'work%';
$prefixLength = strlen($year . 'work') + 1;
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $like, $prefixLength);
$stmt->execute();
$result = $stmt->get_result();
$maxNum = 0;
if ($row = $result->fetch_assoc()) {
    if (preg_match("/^{$year}work(\d+)$/", $row['wId'], $matches)) {
        $maxNum = intval($matches[1]);
    }
}
$newWorkId = $year . "work" . ($maxNum + 1);

// 領隊
$leader = $teamMembers[0];

$conn->begin_transaction(); // 啟用交易

try {
    // 插入 team
    $insertTeamSql = "INSERT INTO team (tId, name, type, leader, uId) VALUES (?, ?, ?, ?, ?)";
    $insertTeamStmt = $conn->prepare($insertTeamSql);
    $insertTeamStmt->bind_param("sssss", $newTeamId, $teamName, $teamType, $leader, $teacherId);
    if (!$insertTeamStmt->execute()) {
        throw new Exception("新增隊伍失敗");
    }

    // 插入 work
    $workState = "待上傳";
    $insertWorkSql = "INSERT INTO work (wId, name, sdgs, state, abstract, tId) VALUES (?, ?, ?, ?, ?, ?)";
    $insertWorkStmt = $conn->prepare($insertWorkSql);
    $insertWorkStmt->bind_param("ssssss", $newWorkId, $workName, $sdgs, $workState, $workAbstract, $newTeamId);
    if (!$insertWorkStmt->execute()) {
        throw new Exception("新增作品失敗");
    }

    // 插入 work_url
    $insertUrlSql = "INSERT INTO work_url (wId, url) VALUES (?, ?)";
    $insertUrlStmt = $conn->prepare($insertUrlSql);
    foreach ($workUrls as $url) {
        $insertUrlStmt->bind_param("ss", $newWorkId, $url);
        if (!$insertUrlStmt->execute()) {
            throw new Exception("新增作品網址失敗");
        }
    }

    // 插入 attendee 表
    $insertAttendeeSql = "INSERT INTO attendee (tId, uId, wId) VALUES (?, ?, ?)";
    $insertAttendeeStmt = $conn->prepare($insertAttendeeSql);

    foreach ($teamMembers as $uId) {
        $insertAttendeeStmt->bind_param("sss", $newTeamId, $uId, $newWorkId);
        if (!$insertAttendeeStmt->execute()) {
            throw new Exception("新增隊員失敗：" . $uId);
        }
    }

    $conn->commit(); // 全部成功
    echo json_encode(["success" => true, "workId" => $newWorkId]);
} catch (Exception $e) {
    $conn->rollback(); // 有錯誤就回滾
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
