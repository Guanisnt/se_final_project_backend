<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header("Content-Type: application/json");

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

// 3. 處理上傳，結果暫存在 $results
$results = [];
foreach ($uploadConfig as $field => $dir) {
    if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
        $fileName       = basename($_FILES[$field]['name']);
        $uniqueName     = uniqid() . '_' . $fileName;
        $targetFilePath = $dir . $uniqueName;

        if (move_uploaded_file($_FILES[$field]['tmp_name'], $targetFilePath)) {
            $results[$field] = [
                'success' => true,
                // 'url'     => 'http://se_final_project_backend.local:8081/api' 
                //              . str_replace('../', '/', $targetFilePath)
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

// 4. 一次輸出所有結果
echo json_encode([
    'success' => true,
    'files'   => $results
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

/* === 從這邊以下開始寫資料庫操作 === */
?>