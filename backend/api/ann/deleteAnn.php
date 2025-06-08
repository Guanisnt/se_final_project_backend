<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header("Content-Type: application/json");
require_once '../db_connect.php';

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "資料庫連線失敗"]);
    exit;
} 

$aId = isset($_GET['aId']) ? intval($_GET['aId']) : null;

if ($aId === null || $aId <= 0) {
    echo json_encode(["success" => false, "error" => "無效的公告ID"]);
    exit;
}

$conn->begin_transaction();

try {
    // 刪除公告海報
    $stmt1 = $conn->prepare("DELETE FROM ann_posterurl WHERE aId = ?");
    $stmt1->bind_param("i", $aId);
    $stmt1->execute();
    $stmt1->close();
    
    // 刪除公告附件
    $stmt2 = $conn->prepare("DELETE FROM ann_file WHERE aId = ?");
    $stmt2->bind_param("i", $aId);
    $stmt2->execute();
    $stmt2->close();
    
    // 刪除公告本身
    $stmt3 = $conn->prepare("DELETE FROM ann WHERE aId = ?");
    $stmt3->bind_param("i", $aId);
    $stmt3->execute();
    
    if ($stmt3->affected_rows > 0) {
        $conn->commit();
        echo json_encode(["success" => true, "message" => "公告刪除成功"]);
    } else {
        $conn->rollback();
        echo json_encode(["success" => false, "error" => "找不到該公告或公告已被刪除"]);
    }
    
    $stmt3->close();
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "error" => "刪除失敗: " . $e->getMessage()]);
}

$conn->close();
?>