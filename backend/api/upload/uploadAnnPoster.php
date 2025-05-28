<?php
require_once '../db_connect.php';

$targetDir = "../uploadImage/annPosters/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$aId = isset($_POST['aId']) ? $_POST['aId'] : null;

if (isset($_FILES["file"])) {
    $fileName = basename($_FILES["file"]["name"]);
    $targetFilePath = $targetDir . uniqid() . "_" . $fileName;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
        // 組 posterUrl
        $posterUrl = "http://se_final_project_backend.local:8081/api" . str_replace("../", "/", $targetFilePath);

        // 插入 ann_posterurl
        $stmt = $conn->prepare("INSERT INTO ann_posterurl (aId, posterUrl) VALUES (?, ?)");
        $stmt->bind_param("ss", $aId, $posterUrl);

        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "aId" => $aId,
                "posterUrl" => $posterUrl
            ]);
        } else {
            echo json_encode(["success" => false, "error" => "資料庫寫入失敗：" . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "檔案上傳失敗"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "沒有檔案被上傳"]);
}

$conn->close();
?>
