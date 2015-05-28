資料夾 devlog 搜尋程式
=========

###說明
    以資料夾命名為核心, 平實的檔案式記錄, 加上 devlog.txt 讓搜尋變的有意義

###用途:
    整理文字, 適當的記錄 tag, 未來可以找到你自己的資料

###環境:
    - linux
    - php 5.6

###install
```sh
    mkdir var/cache
    chmodr -R var
```sh

###權限問題
    1. 使用者要能把記錄檔丟到一個公開的地方, 通常是經由 samba
       在 samba 中會設定誰可以有讀、寫的權限
       請 www-data 這個群組中加入這個 user
       例如: usermod -a -G ken www-data
    2. 如果要利用群組, samba 可以設定 create mask = 0764 (6=rx)

###需求:
    1. 當團隊建立一段時間後, 如果要回頭找以前寫過什麼, 常常會很困難. 需要有統一的地方存資訊, 並關連其它服務
    2. 每個人可以建立自己的 devlog 資料夾, 放置自己寫的資料
    3. devlog 內容可任意修改
    4. devlog 欄位可自行擴充
    5. devlog 格式不正確時, 適當提醒
    6. devlog 必需符合使用者需求, 例如資料夾可以隨時修改, 不要有奇怪的限制
    7. 容易搜尋
    8. 擁有附件
    9. 不搜尋附件, 但搜尋到 devlog 時, 要顯示附件

###使用規範與建議
    如果需要正確的找到資料, tag 命名需要一套標準, 標準由您決定
    建議專有名詞中間的空白用 "-" 符號連接, 例如 google-calendar
    建立 devlog.txt 時, 請使用 UTF-8 without bom 格式

###developer note:
    非核心功能寫成 plugin
    程式功能要少、專注、簡單
    如果 devlog.txt 是空的, 會猜測 folder name 寫入 tag 到檔案中
    devlog.txt 在每次 reindex 都會被修改回存
    tag 搜尋的順序使用加權分數方式
    tag 小寫儲存

###其它問題
    多層目錄下有多個 devlog.txt

###搜尋範例:
    developer 專案F301 jira:tw301 git:8bc2569



