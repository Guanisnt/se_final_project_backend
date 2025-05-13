<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header("Content-Type: application/json");
$input = json_decode(file_get_contents("php://input"), true);
require_once '../db_connect.php';

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "資料庫連線失敗"]);
    exit;
}

// echo json_encode([
//     "success" => true,
//     "userType" => "attendee",
// ]);

// echo json_encode([
//     "success" => true,
//     "data" => [
//         "userType" => "attendee",
//     ]
// ]);

/* === 從這邊以下開始寫資料庫操作，上面我測試API用的誤刪 === */

$uId= $input["id"];
$password= $input["password"];

$sql = "SELECT password, userType FROM users WHERE uId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $uId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) { //是否有帳號
    if ($row["password"] === $password) { //驗證密碼
        $userType = $row["userType"];
        echo json_encode([
            "success" => true,
            "userType" => $userType

        ]);
    } else { //密碼錯誤
        echo json_encode([
            "success" => false,
            "error" => "帳號或密碼錯誤"
        ]);
    }
} else { //帳號錯誤
    echo json_encode([
        "success" => false,
        "error" => "帳號或密碼錯誤"
    ]);
}
$stmt->close();
$conn->close();
?>
