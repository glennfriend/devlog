以檔案記錄為資料核心的搜尋程式
=========

#環境:
    linux
    php 5.6

#權限問題
    1. 使用者要能把記錄檔丟到一個公開的地方, 通常是經由 samba
       在 samba 中會設定誰可以有讀、寫的權限
       請 www-data 這個群組中加入這個 user
       例如: usermod -a -G ken www-data
    2. 如果要利用群組, samba 可以設定 create mask = 0764 (6=rx)

