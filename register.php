<?php  

require_once('functions.php');

session_start();
$errors = array();

// 利用者の登録
if(isset($_POST['submit']) && $_POST['submit'] === "登録") {
    $user = htmlspecialchars($_POST['user'], ENT_QUOTES);
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES);

    // 空チェックと文字数チェック
    if ($user !== "" && mb_strlen($user) >= 2) {
        if ($password !== "" && mb_strlen($password) >= 4) {

            $dbh = db_connect();
            
            $sql = 'INSERT INTO users (user, password) VALUES (?, ?)';   // SQLの命令文
            
            try {

                // PDOStatementインスタンス
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(1, $user, PDO::PARAM_STR);
                $stmt->bindValue(2, password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
                $stmt->execute();
                
                $dbh = null;
                
                header('Location: ./login.php');
                exit();

            } catch(Exception $e) {
                print "データベースの接続に失敗しました： " . $e->getMessage() . "<br/>";
                exit();
            }

        } else {
            $errors['password'] = "パスワードを４文字以上で入力してください";
        }
        
    } else {
        $errors['user'] = "名前を２文字以上で入力してください";

        if ($password === "" || mb_strlen($password) < 4) {
            $errors['password'] = "パスワードを４文字以上で入力してください";
        }
    }
    unset($user);
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>登録画面</title>

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
                <a href="./login.php">ログイン</a>
            </div>
        </header>

        <!-- 登録画面 -->
        <section id="register">
            <div class="inner">
                <h2 class="title">登録画面</h2>

                <form action="register.php" method="post" class="input_info">

                    <?php if (isset($errors['user'])) { echo "<p class='alert'>※".$errors['user']."</p>"; } ?>
                    
                    <div class="user flex">
                        <p>名前</p>
                        <input class="input-text" type="text" name="user" placeholder="名前を入力してください">
                    </div>

                    <?php if (isset($errors['password'])) { echo "<p class='alert'>※".$errors['password']."</p>"; } ?>

                    <div class="password flex">
                        <p>パスワード</p>
                        <input class="input-text" type="text" name="password" placeholder="4文字以上にしてください">
                    </div>
                    
                    <input class="submit-btn" type="submit" name="submit" value="登録">

                </form>
                
            </div>
        </section> <!-- /ログイン画面 -->
        
    </body>
</html>