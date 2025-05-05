# 開發環境設定

## 1. clone 專案資料夾

請先在路徑 `C:/Users/User(你的使用者名稱)/` 打開 cmd 輸入：

```
git clone https://github.com/Guanisnt/se_final_project_backend.git
```

請確認資料夾結構範例如下：

```
C:/Users/User/se_final_project_backend/
├── backend/
│   ├── api/       # 放置 PHP API 檔案
│
```

---

## 2. 設定 hosts 檔案

用系統管理員身分打開記事本，然後開啟 `C:/Windows/System32/drivers/etc/hosts`，新增以下一行：

```
127.0.0.1    se_final_project_backend.local
```

---

## 3. 設定 Apache 的 Virtual Hosts

請打開 Apache 的 `httpd-vhosts.conf` 設定檔，通常位置在：

```
C:/xampp/apache/conf/extra/httpd-vhosts.conf
```

並加入以下內容：

### PHP 後端 API 設定

```apache
# PHP 後端 API
<VirtualHost *:8081>
    ServerName se_final_project_backend.local

    # 將 /api 對應到實體路徑
    Alias /api "C:/Users/User/se_final_project_backend/backend/api"
    <Directory "C:/Users/User/se_final_project_backend/backend/api">
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

## 4. 修改 Apache 監聽 port
到 xampp control panel 中點選 Apache 的 config 中的 httpd.conf，到 listen 8080(8080是自已當初設定的port，可能跟我不一樣) 下面加上 listen 8081(8081是api用到的port)

## 5. 注意事項

- 設定完後記得重新啟動 Apache 伺服器，讓設定生效。
- 後端 PHP API 檔案請放到 `backend/api/`。

---

## 6. 瀏覽方式
- myphpadmin
  ```
  127.0.0.1:8080
  ```
- 後端 API 呼叫範例：
  ```
  http://se_final_project_backend.local:8081/api/你的API.php
  ```

---

## 7. 其他事項
在開發過程中如果要測試請確認以下事項都已完成
- 在後端這個路徑/backend/api/下新增db_connect.php，內容如下，帳號密碼換成自己資料庫的，最後到.gitignore這個檔案裡面看有沒有這一行/backend/api/db_connect.php
  ```
  <?php
  $host = 'localhost';
  $user = 'root';
  $password = '12345678'; // 如有密碼請填寫
  $dbname = 'se_final_project'; // 改成實際資料庫名稱

  //http://se_final_project_backend.local:8081/api/檔名.php 網站連線測試

  // 建立連線
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // 讓資料庫錯誤會拋出 Exception

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
  ```

- 開發時前後端都要遵守以下 API 文件的內容，特別是 request 和 response，json 的 key 命名也一定要完全一樣
  https://docs.google.com/document/d/12qIWhQzDpjkNNAPXB3dxa3b5BNx9iYlK4jR_2Gtfeuo/edit?usp=sharing

---