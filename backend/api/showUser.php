<?php
header('Content-Type: application/json');

require_once 'db_connect.php'; // 載入連線設定 $conn

$sql = "SELECT * FROM users WHERE uid = ?";
$stmt = mysqli_prepare($conn, $sql); // 預備 SQL 語句

$uid = 1;
mysqli_stmt_bind_param($stmt, "i",$uid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// 設定回應格式為 JSON

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode($row);
} else {
    echo json_encode(["message" => "查無資料"]);
}

// 清理資源
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
