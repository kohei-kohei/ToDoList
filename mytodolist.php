<?php  

require_once('functions.php');

session_start();
$errors = array();

$token = "";
$csrf_token = password_hash($token, PASSWORD_DEFAULT);

// CSRF対策
if (!isset($_SESSION['username']) || !isset($_SESSION['token'])) {
    $_SESSION['error'] = "不正なアクセスです";
    header('Location: ./mytodolist.php');
    exit();

} else {
    $user = $_SESSION['username'];
    $dbh = db_connect();

    $sql = 'SELECT * FROM users WHERE user = ?'; // SQLの命令文
    
    try {
        // PDOStatementインスタンス
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $user, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    } catch(Exception $e) {
        print "データベースの接続に失敗しました： " . $e->getMessage() . "<br/>";
        exit();
    }

    if ($user !== $result['user']){
        $_SESSION['error'] = "不正なアクセスです";
        header('Location: ./login.php');
        exit();

    } else {
        $username = $result['user'];
    }
}

// タスクの登録
if(isset($_POST['submit']) && $_POST['submit'] === "追加") {
    $post_token = $_POST['token'];
    
    if (isset($post_token, $_SESSION['token']) && password_verify($token, $_SESSION['token']) && password_verify($token, $post_token)) {
        unset($post_token);
        $task = $_POST['task'];

        // 空チェックと文字数チェック
        if ($task !== "" && mb_strlen($task) >= 2) {
            if (mb_strlen($task) <= 30) {

                $dbh = db_connect();
                
                $sql = 'INSERT INTO tasks (task, user) VALUES (?, ?)';   // SQLの命令文
                
                try {

                    // PDOStatementインスタンス
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(1, $task, PDO::PARAM_STR);
                    $stmt->bindValue(2, $username, PDO::PARAM_STR);
                    $stmt->execute();

                    
                    $dbh = null;

                    header('Location: ./mytodolist.php');
                    exit();

                } catch(Exception $e) {
                    print "データベースの接続に失敗しました： " . $e->getMessage() . "<br/>";
                    exit();
                }

            } else {
                $errors['task'] = "タスクを30文字以下で入力してください";
            } 
            
        } else {
            $errors['task'] = "タスクを２文字以上で入力してください";
        }
        
        unset($task);
        $_SESSION['pretoken'] = $_POST['token'];

    } else {
        // リロードの時はエラーを出さない
        if ($_SESSION['pretoken'] !== $post_token) {
            $_SESSION['error'] = "不正なアクセスです";
        }
        header('Location: ./login.php');
        exit();
    }
}

// 完了ボタンを押したら非表示にする
if(isset($_POST['method']) && ($_POST['method'] === 'put')) {
    $post_token = $_POST['token'];
    
    if (isset($post_token, $_SESSION['token']) && password_verify($token, $_SESSION['token']) && password_verify($token, $post_token)) {
        unset($post_token);
        $id = (int)$_POST['id'];
        
        $dbh = db_connect();

        $sql = "DELETE FROM tasks WHERE id = ?";

        try {

            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $dbh = null;
            
            header('Location: ./mytodolist.php');
            exit();

        } catch(Exception $e) {
            print "データベースの接続に失敗しました： " . $e->getMessage() . "<br/>";
            exit();
        }

    } else {
        $_SESSION['error'] = "不正なアクセスです";
        header('Location: ./login.php');
        exit();
    }
}

// ログアウトを押したらユーザ情報を初期化する
if(isset($_POST['logout']) && ($_POST['logout'] === 'out')) {
    $post_token = $_POST['token'];
    
    if (isset($post_token, $_SESSION['token']) && password_verify($token, $_SESSION['token']) && password_verify($token, $post_token)) {
        unset($post_token);
        unset($_SESSION['username']);

        header('Location: ./index.php');
        exit();

    } else {
        $_SESSION['error'] = "不正なアクセスです";
        header('Location: ./login.php');
        exit();
    }
}

$_SESSION['token'] = $csrf_token;

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Todoリスト</title>

        <!-- Twitterの設定 -->
        <meta name="twitter:card" content="summary" /> 
        <meta name="twitter:site" content="@Kohei_web_nlp" />
        <meta property="og:url" content="https://todolist.ko-hei-blog.com/index.php" /> 
        <meta property="og:title" content="Todoリスト" /> 
        <meta property="og:description" content="Todoリストです" />
        <meta property="og:image" content="https://portfolio.ko-hei-blog.com/img/todolist.png" />

        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>

        <!-- ヘッダー -->
        <header>
            <div class="inner">
                <p>Todoリスト</p>
                <form action="mytodolist.php" method="post">
                    <input type="hidden" name="logout" value="out">

                    <input type="hidden" name="token" value="<?php echo $csrf_token; ?>">
                    <button class="logout-btn" type="submit">ログアウト</button>
                </form>
            </div>
        </header>
                  
        <!-- Todoリスト -->
        <section id="todo">
            <div class="inner">

                <?php
                // 名前の表示
                if (isset($username)) {
                    echo "<div><p class='introduction'>ようこそ、". h($username) ."さん</p></div>";
                }
                ?>

                <h2 class="title">Todoリスト</h2>

                <ul>
                <?php 
                $dbh = db_connect();
                
                $sql = 'SELECT id, task FROM tasks WHERE user = ? ORDER BY id ASC';
                
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(1, $username, PDO::PARAM_STR);
                $stmt->execute();
                
                $dbh = null;
                
                while($tasks = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    print '<li class="flex">';
                    print '<p>' . h($tasks["task"]) . '</p>';

                    print '
                        <form action="mytodolist.php" method="post">
                            <input type="hidden" name="method" value="put">
                            <input type="hidden" name="id" value="'. $tasks['id'] .'">

                            <input type="hidden" name="token" value="'. $csrf_token .'">
                            <button type="submit">完了</button>
                        </form>
                    ';
                    print '</li>';
                }
                ?>
                </ul>

                <form action="mytodolist.php" method="post" class="input_info">

                    <!-- エラーメッセージ -->
                    <?php if (isset($errors['task'])) { echo "<p class='alert'>※".$errors['task']."</p>"; } ?>

                    <div class="task flex">
                        <p>タスク</p>
                        <input class="input-text" type="text" name="task" placeholder="タスクを入力してください">
                    </div>

                    <input type="hidden" name="token" value="<?php echo $csrf_token; ?>">
                    <input class="submit-btn" type="submit" name="submit" value="追加">
                </form>
                
            </div>
        </section> <!-- /Todoリスト -->

    </body>
</html>
