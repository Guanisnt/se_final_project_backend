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

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

echo json_encode([
    "success" => true,
    "teacherId" => "teacher123",
    "page" => 1,
    "totalPage" => 10,
    "data" => [
        [
            "attendYear" => 2025,
            "teamType" => "創意發想組",
            "teamId" => "2025team123",
            "teamName" => "對對隊",
            "workName" => "作品名稱",
            "members" => [
                [
                    "name" => "陳小明",
                    "department" => "資訊工程系",
                ],
                [
                    "name" => "張三",
                    "department" => "資訊工程系",
                ],
            ],
        ]
    ]
]);


/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */

$conn->close();
?>
