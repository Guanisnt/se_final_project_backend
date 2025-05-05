<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');
header("Content-Type: application/json");
$input = json_decode(file_get_contents("php://input"), true);
require_once '../db_connect.php';

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "資料庫連線失敗"]);
    exit;
}

$uid= $input["id"];
$password= $input["password"];
$name= $input["name"];
$sexual= $input["sexual"];
$department= $input["department"];
$grade= $input["grade"];
$email= $input["email"];
$phone= $input["phone"];
$userType= $input["userType"];

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // 讓錯誤會拋出 Exception

$conn->begin_transaction(); // 顯式開始交易

try {
    // 插入 users 表
    $stmt_user = $conn->prepare("INSERT INTO users (uid, name, email, password, sexual, phone, userType) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt_user->bind_param("sssssss", $uid, $name, $email, $password, $sexual, $phone, $userType);
    $stmt_user->execute();

    // 插入 student 表
    $stmt_student = $conn->prepare("INSERT INTO student (department, grade, uid) VALUES (?, ?, ?)");
    $stmt_student->bind_param("sss", $department, $grade, $uid);
    $stmt_student->execute();

    // 提交交易
    $conn->commit();

    echo json_encode(["success" => true]);
} catch (Exception $e) {
    $conn->rollback(); // 取消所有已執行但尚未提交（commit）的 SQL 操作
    echo json_encode([
        "success" => false,
        "error" => "request body資料有缺或是主鍵重複"
    ]);
}

if (isset($stmt_student)) $stmt_student->close();
if (isset($stmt_user)) $stmt_user->close();
$conn->close();
?>
