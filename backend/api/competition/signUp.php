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

// === 解構 JSON ===

$teamName = $input["teamName"];
$teamType = $input["teamType"];
$workName = $input["workName"];
$workAbstract = $input["workAbstract"];
$workUrls = $input["workUrls"]; // array
$sdgs = $input["sdgs"];         // 建議改成 array
$numberOfMember = $input["numberOfMember"];
$teamMembers = $input["teamMembers"]; // array of members
$advisor = $input["advisor"];         // object

// === Debug 輸出 (可用 Postman 測試用) ===
echo json_encode([
    "success" => true,
    "teamName" => $teamName,
    "teamType" => $teamType,
    "workName" => $workName,
    "workUrls" => $workUrls,
    "sdgs" => $sdgs,
    "memberCount" => $numberOfMember,
    "members" => $teamMembers,
    "advisor" => $advisor,
]);

/* === 從這邊以下開始寫資料庫操作 === */
?>
