# 開發環境設定

## 1. 建立專案資料夾

請先在路徑 `C:/Users/User(你的使用者名稱)/` 下建立一個資料夾，命名為：

```
se_final_project
```

資料夾結構範例如下：

```
C:/Users/User/se_final_project/
├── backend/
│   ├── api/       # 放置 PHP API 檔案
│   └── public/    # 放置 Flutter build 出來的靜態網頁 (index.html 等)
```

---

## 2. 設定 hosts 檔案

用系統管理員身分打開記事本，然後開啟 `C:/Windows/System32/drivers/etc/hosts`，新增以下一行：

```
127.0.0.1   se_final_project.local
```

---

## 3. 設定 Apache 的 Virtual Hosts

請打開 Apache 的 `httpd-vhosts.conf` 設定檔，通常位置在：

```
C:/xampp/apache/conf/extra/httpd-vhosts.conf
```

並加入以下內容：

### Flutter 前端設定

```apache
# Flutter 前端
<VirtualHost *:8081>
    ServerName se_final_project.local
    DocumentRoot "C:/Users/User/se_final_project/backend/public"
    <Directory "C:/Users/User/se_final_project/backend/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    DirectoryIndex index.html index.php
    ErrorLog "logs/se_final_project-error.log"
    CustomLog "logs/se_final_project-access.log" common
</VirtualHost>
```

### PHP 後端 API 設定

```apache
# PHP 後端 API
Alias /api "C:/Users/User/se_final_project/backend/api"
<Directory "C:/Users/User/se_final_project/backend/api">
    Options FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

---

## 4. 修改 Apache 監聽 port
到 xampp control panel 中點選 Apache 的 config 中的 httpd.conf，到 listen 8080 下面加上 listen 8081

## 5. 注意事項

- 設定完後記得重新啟動 Apache 伺服器，使設定生效。
- 前端 Flutter build 出來的靜態檔案請放到 `backend/public/`。
- 後端 PHP API 檔案請放到 `backend/api/`。

---

## 6. 瀏覽方式
- myphpadmin
  ```
  127.0.0.1:8080
  ```
- 前端 Flutter 網頁：
  ```
  http://se_final_project.local:8081
  ```
- 後端 API 呼叫範例：
  ```
  http://se_final_project.local:8081/api/你的API.php
  ```

---
