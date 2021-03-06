<?php  
function db_connect() {

    // エラー処理
    try {
        $dsn = 'mysql:dbname=todolist; host=localhost; charset=utf8mb4';
        $user = 'root';
        $password = 'root';

        // PDOインスタンス
        $dbh = new PDO(
            $dsn, 
            $user, 
            $password,
            array (
                PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,   // エラー処理
                PDO::ATTR_EMULATE_PREPARES => false,                    // 静的プレースホルダー
            )
        );
        $dbh->query('SET NAMES utf8mb4');
                    
        return $dbh;
            
    } catch (PDOException $e) {
        print "データベースの接続に失敗しました： " . $e->getMessage() . "<br/>";
        exit();
    }
        
}

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}