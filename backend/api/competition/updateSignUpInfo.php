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

// 驗證必要參數
if (!isset($input['teamId']) || !isset($input['workId']) || 
    !isset($input['workAbstract']) || !isset($input['workUrls']) || 
    !isset($input['sdgs'])) {
    echo json_encode(["success" => false, "error" => "缺少必要參數"]);
    exit;
}

$teamId = $input['teamId'];
$workId = $input['workId'];
$workAbstract = $input['workAbstract'];
$workUrls = $input['workUrls'];
$sdgs = $input['sdgs'];

// 驗證參數格式
if (empty($teamId) || empty($workId) || empty($workAbstract) || 
    !is_array($workUrls) || empty($sdgs)) {
    echo json_encode(["success" => false, "error" => "參數格式不正確"]);
    exit;
}

// 開始事務處理
$conn->begin_transaction();

try {
    // 1. 更新 work 資料表
    $update_work_sql = "UPDATE work SET abstract = ?, sdgs = ? WHERE wId = ? AND tId = ?";
    $update_work_stmt = $conn->prepare($update_work_sql);
    
    if (!$update_work_stmt) {
        throw new Exception("準備更新 work 語句失敗: " . $conn->error);
    }
    
    $update_work_stmt->bind_param("ssss", $workAbstract, $sdgs, $workId, $teamId);
    
    if (!$update_work_stmt->execute()) {
        throw new Exception("更新 work 失敗: " . $update_work_stmt->error);
    }
    
    // 檢查是否有更新到資料
    if ($update_work_stmt->affected_rows === 0) {
        throw new Exception("找不到對應的作品資料");
    }
    
    $update_work_stmt->close();
    
    // 2. 刪除舊的 work_url 記錄
    $delete_urls_sql = "DELETE FROM work_url WHERE wId = ?";
    $delete_urls_stmt = $conn->prepare($delete_urls_sql);
    
    if (!$delete_urls_stmt) {
        throw new Exception("準備刪除 URL 語句失敗: " . $conn->error);
    }
    
    $delete_urls_stmt->bind_param("s", $workId);
    
    if (!$delete_urls_stmt->execute()) {
        throw new Exception("刪除舊 URL 失敗: " . $delete_urls_stmt->error);
    }
    
    $delete_urls_stmt->close();
    
    // 3. 插入新的 work_url 記錄
    $insert_url_sql = "INSERT INTO work_url (url, wId) VALUES (?, ?)";
    $insert_url_stmt = $conn->prepare($insert_url_sql);
    
    if (!$insert_url_stmt) {
        throw new Exception("準備插入 URL 語句失敗: " . $conn->error);
    }
    
    foreach ($workUrls as $url) {
        $url = trim($url); // 去除前後空白
        if (!empty($url)) {
            $insert_url_stmt->bind_param("ss", $url, $workId);
            if (!$insert_url_stmt->execute()) {
                throw new Exception("插入 URL 失敗: " . $insert_url_stmt->error);
            }
        }
    }
    
    $insert_url_stmt->close();
    
    // 提交事務
    $conn->commit();
    
    echo json_encode([
        "success" => true,
        "message" => "報名資訊更新成功"
    ]);
    
} catch (Exception $e) {
    // 回滾事務
    $conn->rollback();
    
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}

$conn->close();
?>
