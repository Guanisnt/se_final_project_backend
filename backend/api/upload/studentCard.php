<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header("Content-Type: application/json");

require_once '../db_connect.php';

$stmt = $conn->prepare("UPDATE attendee SET studentCard = ? WHERE uId = ?");
$stmt->bind_param("ss", $imageUrl, $uId);

$targetDir = "../uploadImage/studentCards/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$uId = isset($_POST['uId']) ? $_POST['uId'] : null;

if (isset($_FILES["file"])) {
    $fileName = basename($_FILES["file"]["name"]);
    $targetFilePath = $targetDir . uniqid() . "_" . $fileName;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
        $imageUrl = "http://se_final_project_backend.local:8081/api" . str_replace("../", "/", $targetFilePath);
        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "uId" => $uId,
                "imgUrl" => $imageUrl
            ]);
        } else {
            echo json_encode(["success" => false, "error" => "資料庫寫入失敗：" . $stmt->error]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Upload failed."]);
    }
} else {
    echo json_encode(["success" => false, "error" => "No file uploaded."]);
}

/* === 從這邊以下開始寫資料庫操作 === */


$stmt->close();
$conn->close();