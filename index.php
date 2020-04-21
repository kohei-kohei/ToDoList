<?php  

require_once('functions.php');

// タスクの登録
if(isset($_POST['submit'])) {
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES);

    $dbh = db_connect();

    $sql = 'INSERT INTO tasks (name, done) VALUES (?, 0)';   // SQLの命令文

    // PDOStatementインスタンス
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(1, $name, PDO::PARAM_STR);
    $stmt->execute();

    $dbh = null;

    unset($name);    
}

// 完了ボタンを押したら非表示にする
if(isset($_POST['method']) && ($_POST['method'] === 'put')) {
    $id = htmlspecialchars($_POST['id'], ENT_QUOTES);
    $id = (int)$id;

    $dbh = db_connect();

    $sql = 'UPDATE tasks SET done = 1 WHERE id = ?';
    $stmt = $dbh->prepare($sql);

    $stmt->bindValue(1, $id, PDO::PARAM_INT);
    $stmt->execute();

    $dbh = null;
}
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
        <meta property="og:title" content="Kohei's Portfolio" /> 
        <meta property="og:description" content="こーへいのポートフォリオサイトです" />
        <meta property="og:image" content="https://kohei-kohei.github.io/portfolio/img/dubai.jpg" />

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">

        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>
                  
        <!-- Todoリスト -->
        <section id="todo">
            <div class="inner">
                <h2 class="title">Todoリスト</h2>

                <ul>
                <?php 
                $dbh = db_connect();
                
                $sql = 'SELECT id, name FROM tasks WHERE done = 0 ORDER BY id ASC';
                
                $stmt = $dbh->prepare($sql);
                $stmt->execute();
                
                $dbh = null;
                
                while($task = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    print '<li class="flex">';
                    print '<p>' . $task["name"] . '</p>';

                    print '
                        <form action="index.php" method="post">
                            <input type="hidden" name="method" value="put">
                            <input type="hidden" name="id" value=" ' . $task['id'] .' ">
                            <button type="submit">完了</button>
                        </form>
                    ';
                    print '</li>';
                }
                ?>
                </ul>

                <form action="index.php" method="post" class="input_task">

                    <div class="name flex">
                        <p>タスク</p>
                        <input class="input-text" type="text" name="name" placeholder="タスクを入力してください">
                    </div>

                    <input class="submit-btn" type="submit" name="submit" value="登録">
                </form>
                
            </div>
        </section> <!-- /Todoリスト -->
        
    </body>
</html>