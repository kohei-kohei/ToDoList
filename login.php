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

        <!-- ログイン画面 -->
        <section id="login">
            <div class="inner">
                <h2 class="title">ログイン</h2>

                <form action="index.php" method="post" class="input_info">

                    <div class="user-name flex">
                        <p>名前</p>
                        <input class="input-text" type="text" name="user-name" placeholder="名前を入力してください">
                    </div>

                    <div class="password flex">
                        <p>パスワード</p>
                        <input class="input-text" type="text" name="password" placeholder="パスワードを入力してください">
                    </div>

                    <input class="submit-btn" type="submit" name="register" value="登録">
                    <input class="submit-btn" type="submit" name="login" value="ログイン">
                </form>
                
            </div>
        </section> <!-- /ログイン画面 -->
        
    </body>
</html>