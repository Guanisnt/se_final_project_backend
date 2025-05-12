<?php
$targetDir = "uploads/student_cards/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (isset($_FILES["file"])) {
    $fileName = basename($_FILES["file"]["name"]);
    $targetFilePath = $targetDir . uniqid() . "_" . $fileName;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
        // 回傳圖片 URL
        $imageUrl = "https://yourdomain.com/" . $targetFilePath;
        echo json_encode(["success" => true, "url" => $imageUrl]);
    } else {
        echo json_encode(["success" => false, "message" => "Upload failed."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No file uploaded."]);
}
