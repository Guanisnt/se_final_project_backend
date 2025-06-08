<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header("Content-Type: application/json");
#$input = json_decode(file_get_contents("php://input"), true);
require_once '../db_connect.php';

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "資料庫連線失敗"]);
    exit;
}
// 主題、時間、講師姓名頭銜、報名上限、已報名人數

// echo json_encode([
//     "success" => true,
//     "data" => [
//         [
//             "id" => 1,
//             "topic" => "工作坊主題",
//             "date" => "2025-05-01",
//             "lecturerName" => "講師姓名",
//             "lecturerTitle" => "講師頭銜",
//             "maxAmount" => 30,
//             "currentAmount" => 15,
//         ],
//         [
//             "id" => 2,
//             "topic" => "工作坊主題",
//             "date" => "2025-05-01",
//             "lecturerName" => "講師姓名",
//             "lecturerTitle" => "講師頭銜",
//             "maxAmount" => 30,
//             "currentAmount" => 15,
//         ],
//     ],
// ]);

/* === 從這邊以下開始寫資料庫操作 === */

$sql = "
SELECT 
    workshop.wsId,
    workshop.topic,
    DATE(workshop.time) AS date,
    workshop.maxAmount,
    workshop.currentAmount,
    users.name AS lecturerName,
    lecturer.title AS lecturerTitle
FROM 
    workshop
JOIN 
    lecturer ON workshop.lecturerId = lecturer.uId
JOIN 
    users ON lecturer.uId = users.uId
";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) { //fetch_assoc() 可能會自動轉型成字串
    $data[] = [
        "wsId" => (int) $row["wsId"],
        "topic" => $row["topic"],
        "date" => $row["date"],
        "maxAmount" => (int) $row["maxAmount"],
        "currentAmount" => (int) $row["currentAmount"],
        "lecturerName" => $row["lecturerName"],
        "lecturerTitle" => $row["lecturerTitle"],
    ];
}
echo json_encode([
    "success" => true,
    "data" => $data
]);


$conn->close();
?>