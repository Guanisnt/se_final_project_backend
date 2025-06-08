<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header("Content-Type: application/json");

require_once '../db_connect.php';

// 1. 設定各檔案對應的資料夾
$uploadConfig = [
    'affidavit'     => '../uploadFiles/affidavits/',
    'consent'       => '../uploadFiles/consents/',
    'introduction'  => '../uploadFiles/introductions/',
];

// 2. 確保資料夾都存在
foreach ($uploadConfig as $key => $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

// 3. 檢查必要參數
$workId = isset($_POST['workId']) ? $_POST['workId'] : null;

if (!$workId) {
    echo json_encode([
        'success' => false,
        'error' => '缺少 workId'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 4. 處理上傳，結果暫存在 $results
$results = [];
$updateFields = [];

foreach ($uploadConfig as $field => $dir) {
    if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
        $fileName       = basename($_FILES[$field]['name']);
        $uniqueName     = uniqid() . '_' . $fileName;
        $targetFilePath = $dir . $uniqueName;

        if (move_uploaded_file($_FILES[$field]['tmp_name'], $targetFilePath)) {
            // 更新欄位準備
            $updateFields[$field] = $targetFilePath;

            $results[$field] = [
                'success' => true
            ];
        } else {
            $results[$field] = [
                'success' => false,
                'error'   => 'Move failed.'
            ];
        }
    } else {
        $results[$field] = [
            'success' => false,
            'error'   => 'No file uploaded or upload error.'
        ];
    }
}

// 5. 更新資料庫
if (!empty($updateFields)) {
    // 準備動態更新欄位
    $setStr = '';
    $params = [];
    $types = '';
    foreach ($updateFields as $field => $filePath) {
        $setStr .= "$field = ?, ";
        $params[] = $filePath;
        $types .= 's';
    }

    // 移除最後的逗號
    $setStr = rtrim($setStr, ', ');

    // 條件
    $params[] = $workId;
    $types .= 's';

    $sql = "UPDATE work SET $setStr WHERE wId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        echo json_encode([
            'success' => false,
            'error' => '資料庫更新失敗'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt->close();
}

echo json_encode([
    'success' => true,
    'files'   => $results
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>
