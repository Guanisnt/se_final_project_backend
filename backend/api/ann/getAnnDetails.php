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
//             "posters/poster_1.jpg",
//             "posters/poster_2.jpg"
//         ],
//         "files" => [
//             [
//                 "url" => "https://yourcdn.com/file/3_1.pdf",
//                 "name" => "fileName1"
//             ],
//             [
//                 "url" => "https://yourcdn.com/file/3_2.doc",
//                 "name" => "fileName2"
//             ]
//         ]
//     ]
// ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */

if (isset($_GET['aId'])) {
    $aId = intval($_GET['aId']);

    // 查詢公告主資料
    $stmt_ann = $conn->prepare("SELECT aId, title, content, time FROM ann WHERE aId = ?");
    $stmt_ann->bind_param("i", $aId);
    $stmt_ann->execute();
    $result_ann = $stmt_ann->get_result();

    if ($result_ann->num_rows === 0) {
        echo json_encode([
            "success" => false,
            "error" => "找不到此公告，檢查aId"
        ]);
        exit;
    }

    $ann_data = $result_ann->fetch_assoc();
    $stmt_ann->close();

    // 查詢檔案資料
    $stmt_file = $conn->prepare("SELECT fileUrl, fileName FROM file WHERE aId = ?");
    $stmt_file->bind_param("i", $aId);
    $stmt_file->execute();
    $result_file = $stmt_file->get_result();

    $files = [];
    while ($row = $result_file->fetch_assoc()) {
        $files[] = [
            "url" => $row['fileUrl'],
            "name" => $row['fileName']
        ];
    }
    $stmt_file->close();

    // 查詢 posterUrl
    $stmt_posters = $conn->prepare("SELECT posterUrl FROM ann_posterurl WHERE aId = ?");
    $stmt_posters->bind_param("i", $aId);
    $stmt_posters->execute();
    $result_posters = $stmt_posters->get_result();

    $posterUrls = [];
    while ($row = $result_posters->fetch_assoc()) {
        $posterUrls[] = $row['posterUrl'];
    }
    $stmt_posters->close();

    // 輸出 JSON 結果
    echo json_encode([
        "success" => true,
        "data" => [
            "aId" => $ann_data['aId'],
            "title" => $ann_data['title'],
            "content" => $ann_data['content'],
            "time" => $ann_data['time'],
            "posterUrls" => $posterUrls,
            "files" => $files
        ]
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "缺少aId參數"
    ]);
}

$conn->close();
?>
