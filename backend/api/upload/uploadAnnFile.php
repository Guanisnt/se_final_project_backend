<?php
require_once '../db_connect.php';

$targetDir = "../uploadFiles/annFiles/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$aId = isset($_POST['aId']) ? $_POST['aId'] : null;

if (isset($_FILES["file"])) {
    $fileName = basename($_FILES["file"]["name"]);
    $targetFilePath = $targetDir . uniqid() . "_" . $fileName;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
        // 檔案成功上傳，組成檔案 URL
        $fileUrl = "http://se_final_project_backend.local:8081/api" . str_replace("../", "/", $targetFilePath);

        // 插入 ann_file
        $insertSql = "INSERT INTO ann_file (fileName, fileUrl, aId) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("sss", $fileName, $fileUrl, $aId);

        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "fileName" => $fileName,
                "fileUrl" => $fileUrl,
                "aId" => $aId
            ]);
        } else {
            echo json_encode(["success" => false, "error" => "資料庫插入失敗"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "檔案上傳失敗"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "未收到檔案"]);
}
?>
