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

$uId = isset($_GET['uId']) ? $_GET['uId'] : null;
$name = isset($input['name']) ? $input['name'] : null;
$email = isset($input['email']) ? $input['email'] : null;
$phone = isset($input['phone']) ? $input['phone'] : null;
$sexual = isset($input['sexual']) ? $input['sexual'] : null;
$judgeInfo = isset($input['judgeInfo']) ? $input['judgeInfo'] : null;
$teacherInfo = isset($input['teacherInfo']) ? $input['teacherInfo'] : null;
$studentInfo = isset($input['studentInfo']) ? $input['studentInfo'] : null;
$lectureInfo = isset($input['lectureInfo']) ? $input['lectureInfo'] : null;
$attendeeInfo = isset($input['attendeeInfo']) ? $input['attendeeInfo'] : null;

// echo json_encode([
//     "success" => true,
//     "name" => $name,
//     "email" => $email,
//     "phone" => $phone,
//     "sexual" => $sexual,
//     "judgeInfo" => $judgeInfo,
//     "teacherInfo" => $teacherInfo,
//     "studentInfo" => $studentInfo,
//     "lectureInfo" => $lectureInfo,
//     "attendeeInfo" => $attendeeInfo,
// ]);


/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */

$conn->close();
?>
