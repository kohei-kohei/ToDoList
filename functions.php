<?php  
function db_connect() {

    // エラー処理
    try {
        $dsn = 'mysql:dbname=todolist;host=localhost;charset=utf8';
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
            $dbh->query('SET NAMES utf8');
                        
            return $dbh;
            
    } catch (PDOException $e) {
        print "エラー： " . $e->getMessage() . "<br/>";
        exit();
    }
        
}