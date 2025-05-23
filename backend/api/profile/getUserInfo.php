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

// 模擬回傳一筆公告資料
echo json_encode([
    "success" => true,
    "uId" => $uId,
    "name" => "陳小明",
    "email" => "a1115526@gmail.com",
    "phone" => "0912345678",
    "sexual" => "男",
    "userType" => "依照uId回傳的使用者類型",
    "judgeInfo" => [
        "title" => "中壢來的不中立評審",
    ],
    "teacherInfo" => [
        "department" => "資訊工程學系",
        "organization" => "高雄大學",
        "title" => "高大尚",
    ],
    "studentInfo" => [
        "department" => "資訊工程學系",
        "grade" => "大五",
    ],
    "lectureInfo" => [
        "title" => "講幹話大師",
    ],
    "attendeeInfo" => [
        "studentCard" => "回傳學生證圖片路徑",
        "teamId" => "回傳隊伍ID",
        "workId" => "回傳作品ID",
    ],
]);


/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */

$conn->close();
?>
