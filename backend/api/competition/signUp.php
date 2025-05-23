<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header("Content-Type: application/json");

// 解析 JSON request body
$input = json_decode(file_get_contents("php://input"), true);
require_once '../db_connect.php';

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "資料庫連線失敗"]);
    exit;
}

echo json_encode([
    "success" => true,
    // "members" => [
    //     [
    //         "name" => "這是隊員一名字",
    //         "stuId" => "這是學號",
    //         "department" => "資工系",
    //         "grade" => "大三",
    //         "email" => "hihihi@gmail",
    //         "phone" => "091212121212",
    //         "studentCard" => "學生證存在後端的URL",
    //         "sexual" => "男"
    //     ],
    //     [
    //         "name" => "這是隊員二名字",
    //         "stuId" => "這是學號",
    //         "department" => "資工系",
    //         "grade" => "大三",
    //         "email" => "hihihi@gmail",
    //         "phone" => "091212121212",
    //         "sexual" => "女"
    //     ]
    // ],
    // "advisor" => [
    //     "teacherName" => "指導教授姓名",
    //     "title" => "職稱",
    //     "organization" => "高雄大學"
    // ]
]);

/* === 從這邊以下開始寫資料庫操作 === */
#先找當年度的隊伍遞增

?>
