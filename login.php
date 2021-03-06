<?php  

require_once('functions.php');

session_start();
$errors = array();

$token = "";
$csrf_token = password_hash($token, PASSWORD_DEFAULT);

// 利用者のログイン
if(isset($_POST['submit']) && $_POST['submit'] === "ログイン") {
    $post_token = $_POST['token'];
    
    if (isset($post_token, $_SESSION['token']) && password_verify($token, $_SESSION['token']) && password_verify($token, $post_token)) {
        unset($post_token);
        $user = $_POST['user'];
        $password = $_POST['password'];
        
        // 空チェックと文字数チェック
        if ($user !== "" && mb_strlen($user) >= 2) {
            if ($password !== "" && mb_strlen($password) >= 4) {
                
                $dbh = db_connect();
                
                $sql = 'SELECT * FROM users WHERE user = ?'; // SQLの命令文
                
                try {
                    
                    // PDOStatementインスタンス
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(1, $user, PDO::PARAM_STR);
                    $stmt->execute();
                    
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (password_verify($password, $result['password'])) {
                        $_SESSION['username'] = $user; 
                        header('Location: ./mytodolist.php');
                        exit();
                    } else {
                        $errors['login'] = '名前あるいはパスワードに誤りがあります';
                    } 
                    
                    $dbh = null;
                    
                } catch(Exception $e) {
                    print "データベースの接続に失敗しました： " . $e->getMessage() . "<br/>";
                    exit();
                }
                
            } else {
                $errors['password'] = "パスワードを入力してください";
            }
            
        } else {
            $errors['user'] = "名前を入力してください";

            if ($password === "" || mb_strlen($password) < 4) {
                $errors['password'] = "パスワードを入力してください";
            }
        }

        unset($user);
        unset($password);
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
$_SESSION['token'] = $csrf_token;
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ログイン画面</title>

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
                <a href="./register.php" class="register-btn">登録</a>
            </div>
        </header>


        <!-- ログイン画面 -->
        <section id="login">
            <div class="inner">
                
                <?php
                // 不正アクセスの通告
                if (isset($_SESSION['error'])) {
                    echo "<p class='alert'>※". $_SESSION['error'] ."</p>";
                    unset($_SESSION['error']);
                }
                ?>
                
                <h2 class="title">ログイン画面</h2>

                <form action="login.php" method="post" class="input_info">

                    <?php if (isset($errors['login'])) { echo "<p class='alert'>※".$errors['login']."</p>"; } ?>
                    <?php if (isset($errors['user'])) { echo "<p class='alert'>※".$errors['user']."</p>"; } ?>

                    <div class="user flex">
                        <p>名前</p>
                        <input class="input-text" type="text" name="user" placeholder="名前を入力してください">
                    </div>

                    <?php if (isset($errors['password'])) { echo "<p class='alert'>※".$errors['password']."</p>"; } ?>

                    <div class="password flex">
                        <p>パスワード</p>
                        <input class="input-text" type="password" name="password" placeholder="パスワードを入力してください">
                    </div>

                    <input type="hidden" name="token" value="<?php echo $csrf_token; ?>">
                    <input class="submit-btn" type="submit" name="submit" value="ログイン">

                </form>

                <div class="page-link">
                    <a href="./index.php">&larr;デモページに戻る</a>
                    <a href="./register.php" class="register-text">まだ登録されていない方&rarr;</a>
                </div>
                
            </div>
        </section> <!-- /ログイン画面 -->
        
    </body>
</html>