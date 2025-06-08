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

if (!isset($_GET['uId'])) {
    echo json_encode(["success" => false, "error" => "缺少 uId 參數"]);
    exit;
}

$uId = $_GET['uId'];

// 查詢基本資料與 userType
$stmt = $conn->prepare("SELECT uId, name, email, phone, sexual, userType FROM users WHERE uId = ?");
$stmt->bind_param("s", $uId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "error" => "使用者不存在"]);
    exit;
}

$user = $result->fetch_assoc();
$response = [
    "success" => true,
    "uId" => $user["uId"],
    "name" => $user["name"],
    "email" => $user["email"],
    "phone" => $user["phone"],
    "sexual" => $user["sexual"],
    "userType" => $user["userType"]
];

// 根據 userType 加入額外資訊
switch ($user["userType"]) {
    case "student":
        $stmt = $conn->prepare("SELECT department, grade FROM student WHERE uId = ?");
        $stmt->bind_param("s", $uId);
        $stmt->execute();
        $stmt->bind_result($department, $grade);
        if ($stmt->fetch()) {
            $response["studentInfo"] = [
                "department" => $department,
                "grade" => $grade
            ];
        }
        break;

    case "teacher":
        $stmt = $conn->prepare("SELECT department, organization, title FROM teacher WHERE uId = ?");
        $stmt->bind_param("s", $uId);
        $stmt->execute();
        $stmt->bind_result($department, $organization, $title);
        if ($stmt->fetch()) {
            $response["teacherInfo"] = [
                "department" => $department,
                "organization" => $organization,
                "title" => $title
            ];
        }
        break;

    case "lecturer":
        $stmt = $conn->prepare("SELECT title FROM lecturer WHERE uId = ?");
        $stmt->bind_param("s", $uId);
        $stmt->execute();
        $stmt->bind_result($title);
        if ($stmt->fetch()) {
            $response["lectureInfo"] = [
                "title" => $title
            ];
        }
        break;

    case "judge":
        $stmt = $conn->prepare("SELECT title FROM judge WHERE uId = ?");
        $stmt->bind_param("s", $uId);
        $stmt->execute();
        $stmt->bind_result($title);
        if ($stmt->fetch()) {
            $response["judgeInfo"] = [
                "title" => $title
            ];
        }
        break;

    case "attendee":
        $stmt = $conn->prepare("SELECT a.tId, a.wId, a.studentCard, s.department, s.grade 
                                FROM attendee a 
                                JOIN student s ON a.uId = s.uId 
                                WHERE a.uId = ?");
        $stmt->bind_param("s", $uId);
        $stmt->execute();
        $stmt->bind_result($tId, $wId, $studentCard, $department, $grade);
        if ($stmt->fetch()) {
            $response["attendeeInfo"] = [
                "studentCard" => $studentCard,
                "teamId" => $tId,
                "workId" => $wId
            ];
            $response["studentInfo"] = [
                "department" => $department,
                "grade" => $grade
            ];
        }
        break;
}

echo json_encode($response);
$stmt->close();
$conn->close();
?>


