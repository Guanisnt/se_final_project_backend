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
// else {echo json_encode(["success" => true]);}
// $aId = isset($_GET['aId']) ? intval($_GET['aId']) : null;

// if (!$aId) {
//     echo json_encode([
//         "success" => false,
//         "error" => "缺少 aId 參數"
//     ]);
//     exit;
// }

// // 模擬回傳一筆公告資料
// echo json_encode([
//     "success" => true,
//     "data" => [
//         "aId" => $aId,
//         "title" => "系統維護公告",
//         "content" => "系統將於凌晨 2 點進行例行維護，屆時將暫停服務。",
//         "time" => "2025-05-01",
//         "posterUrls" => [
//             "http://se_final_project_backend.local:8081/public/posters/poster_1.jpg",
//             "http://se_final_project_backend.local:8081/public/posters/poster_2.jpg"
//         ] ,
//         "fileUrls" => [
//           "https://yourcdn.com/file/3_1.pdf",
//           "https://yourcdn.com/file/3_2.doc"
//         ] ,
//         "fileNames" => [
//             "fileName1",
//             "fileName2"
//         ]
//     ]
// ]);

/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */

$conn->close();
?>