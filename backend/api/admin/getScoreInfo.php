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
$year = isset($_GET['year']) ? $_GET['year'] : null;
$teamType = isset($_GET['teamType']) ? $_GET['teamType'] : null;

echo json_encode([
    "success" => true,
    "page" => $page,
    "totalPages" => 50,
    "data" => [
        [
            "teamType" => "創意發想組",
            "teamName" => "創意團隊A",
            "workName" => "創意作品1",
            "score" => 85,
        ],
        [
            "teamType" => "創意發想組",
            "teamName" => "創意團隊B",
            "workName" => "創意作品2",
            "score" => 90,
        ],
    ]
]);

/* === 從這邊以下開始寫資料庫操作 === */

?>