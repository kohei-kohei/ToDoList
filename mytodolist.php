<?php  

require_once('functions.php');

session_start();
$errors = array();

$token = htmlspecialchars($_SESSION['token'], ENT_QUOTES);
$csrf_token = htmlspecialchars(base64_encode(random_bytes(32)), ENT_QUOTES);

// CSRF対策
if (!isset($_SESSION['username']) || !isset($_SESSION['token'])) {
    $_SESSION['error'] = "不正なアクセスです";
    header('Location: ./login.php');
    exit();

} else {
    $user = htmlspecialchars($_SESSION['username']);
    $dbh = db_connect();

    $sql = 'SELECT * FROM users WHERE user = ?'; // SQLの命令文
    
    // PDOStatementインスタンス
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(1, $user, PDO::PARAM_STR);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
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
    $task = htmlspecialchars($_POST['task'], ENT_QUOTES);

    $post_token = htmlspecialchars($_POST['token'], ENT_QUOTES);

    if (isset($post_token, $_SESSION['token']) && ($post_token === $_SESSION['token'])) {
        unset($post_token);

        // 空チェックと文字数チェック
        if ($task !== "" && mb_strlen($task) >= 2) {

            $dbh = db_connect();
            
            $sql = 'INSERT INTO tasks (task, user) VALUES (?, ?)';   // SQLの命令文
            
            // PDOStatementインスタンス
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1, $task, PDO::PARAM_STR);
            $stmt->bindValue(2, $username, PDO::PARAM_STR);
            $stmt->execute();
            
            $dbh = null;

            header('Location: ./mytodolist.php');
            exit();
            
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

    $id = htmlspecialchars($_POST['id'], ENT_QUOTES);
    $id = (int)$id;
    $post_token = htmlspecialchars($_POST['token'], ENT_QUOTES);

    if (isset($post_token, $_SESSION['token']) && ($post_token === $_SESSION['token'])) {
        unset($post_token);

        $dbh = db_connect();

        $sql = "DELETE FROM tasks WHERE id = ?";

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        $stmt->execute();

        $dbh = null;

        header('Location: ./mytodolist.php');
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
        <meta property="og:url" content="https://kohei-kohei.github.io/portfolio/" /> 
        <meta property="og:title" content="Todoリスト" /> 
        <meta property="og:description" content="Todoリストです" />
        <meta property="og:image" content="https://kohei-kohei.github.io/portfolio/img/dubai.jpg" />

        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>

        <!-- ヘッダー -->
        <header>
            <div class="inner">
                <p>Todoリスト</p>
                <a href="./index.php" class="logout-btn">ログアウト</a>
            </div>
        </header>
                  
        <!-- Todoリスト -->
        <section id="todo">
            <div class="inner">

                <?php
                // 名前の表示
                if (isset($username)) {
                    echo "<div><p class='introduction'>ようこそ、". $username ."さん</p></div>";
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
                    print '<p>' . $tasks["task"] . '</p>';

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