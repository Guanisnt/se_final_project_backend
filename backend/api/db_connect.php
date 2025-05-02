<?php
$host = 'localhost';
$user = 'root';
$password = '12345678'; // 如有密碼請填寫
$dbname = 'se_final_project'; // 改成實際資料庫名稱

//http://se_final_project_backend.local:8081/api/檔名.php 網站連線

// 建立連線
$conn = mysqli_connect($host, $user, $password, $dbname);
// 檢查連線
if (!$conn) {
    #echo "資料庫連線失敗: " . mysqli_connect_error();
    die("資料庫連線失敗: " . mysqli_connect_error());
}
else{
    #echo "資料庫連線成功";
} 
?>
