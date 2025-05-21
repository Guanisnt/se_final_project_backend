<?php
$targetDir = "../uploadImage/annPosters/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$aId = isset($_POST['aId']) ? $_POST['aId'] : null;

if (isset($_FILES["file"])) {
    $fileName = basename($_FILES["file"]["name"]);
    $targetFilePath = $targetDir . uniqid() . "_" . $fileName;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
        $imageUrl = "http://se_final_project_backend.local:8081/api" . str_replace("../", "/", $targetFilePath);
        echo json_encode([
            "success" => true,
            // "imgUrl" => $imageUrl,
            // "aId" => $aId
        ]);
    } else {
        echo json_encode(["success" => false, "error" => "Upload failed."]);
    }
} else {
    echo json_encode(["success" => false, "error" => "No file uploaded."]);
}

/* === 從這邊以下開始寫資料庫操作 === */