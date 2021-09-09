<?php
session_start();
session_regenerate_id(true);
ini_set('memory_limit', '1500M');
require_once("./util.php");
set_error_handler('noticeCallback', E_NOTICE);
?>
<!DOCTYPE HTML>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <title>誰でもメガネをかけている女子中学生</title>
    <link href="./css/utilStyle.css" rel="stylesheet">
    <link href="./css/popup.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Yusei+Magic&display=swap" rel="stylesheet">
</head>

<body class="pt-5">
    <?php
    if (
        (!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
        (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443')
    ) {
        $server_request_scheme = 'https';
    } else {
        $server_request_scheme = 'http';
    }
    //httpかhttpsかの判定がこれで出来るはずだがngrokで試してる範疇では何故かどっちにしてもhttpしか返してくれなかった

    $callback_url = $server_request_scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $homeUrl = preg_replace('/\?.*/', '', $callback_url);

    ?>
    <div class="headerBrank"></div>

    <header>
        <nav class="navbar navbar-expand-lg fixed-top border" style="background-color:#FFFFFF">
            <div class="navbar-brand linkWrapper headerContext">
                <img src="imgSvg/glasses.jpg" class="glasses" alt="腐女子のイラスト"></img>
                <div class="siteTitle">
                    <span>誰でもメガネをかけている</span>
                    <soan>女子中学生</span>
                    <a href="<?php print $homeUrl ?>" class="link"></a>
                </div>
            </div>
            <div class="menuBtnWrapper">
                <input id="menuOpen" type="checkbox" class="cbHidden">
                <label for="menuOpen" class="menuBtn">
                    <svg width="24" height="24" class="hamburger" viewBox="0 0 46 46" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g id="Group1">
                            <path id="bar1" d="M3 3H43" stroke="black" stroke-width="4.68085" stroke-linecap="round" />
                            <path id="bar2" d="M3 23H43" stroke="black" stroke-width="4.68085" stroke-linecap="round" />
                            <path id="bar3" d="M3 43H43" stroke="black" stroke-width="4.68085" stroke-linecap="round" />
                        </g>
                    </svg>
                </label>
                <div class="menuWrapper">
                    <div class="menuContext linkWrapper">
                        Tweet
                        <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" target="_blank" class="share_button link" data-text="メガネをかけている女子中学生になりましょう？？？" data-size="large" data-show-count="false" data-url=$homeUrl></a>
                        <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                    </div>
                    <div class="menuContext linkWrapper">
                        GitHub
                        <a href="https://github.com/SystemEngineeringTeam/hack-teamD-2021-summer" target="_blank" class="link"></a>
                    </div>
                    <div class="menuContext linkWrapper">
                        連携解除
                        <a href="https://twitter.com/settings/applications/21753936" target="_blank" class="link"></a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="container-fluid pt-3">

        <div class="row justify-content-md-center">

            <div class="col">

                <?php
                require_once("igo-php-0.1.7/lib/Igo.php");
                $_SESSION["oauth_token_secret"] = '';

                $igo = new Igo('ipadic', 'UTF-8');

                $api_key = "KFYqe6m5v6BMw3nt5U9lQtfIX";
                $api_secret = "5vIVRCjtOSA3RD9rWCTzV7CDYifnTzVLtMO26IvPR41TiVQC6B";
                $my_access_token = "1272371880595296256-ot0XuuYwLgsjQO8sDAaedqMsQPAAGb";
                $my_access_token_secret = "SzwF0VdpdsmZiPqG7g9YW7Jg8661Gc71GJY7XEwfLvJuM";

                try {
                    if (isset($_GET["oauth_token"]) || isset($_GET["oauth_verifier"])) {
                        $query = getAccessToken($api_key, $api_secret);
                        $tlInfo = getHomeTimeLine($api_key, $api_secret, $query["oauth_token"], $query["oauth_token_secret"]);
                        list($json, $header) = $tlInfo;

                        getTimeLineTransform($json, $header, $igo);
                    } else if (isset($_GET["denied"])) {
                        print "<p>連携が拒否されました。</p>";
                        exit();
                    } else {
                        $query = getRequestToken($api_key, $api_secret, $callback_url);
                        print '<div class="description">
                                <h3>TLの人たちがメガネをかけている女子中学生になります。</h3>
                                <div class="overView">
                                    <p>認証したあなたのTLを取得して、痛い女子中学生語に変換します。</p>
                                    <p>元ネタはこちら:<a href=https://twitter.com/glasses_jc>メガネをかけている女子中学生bot</a></p>
                                </div><br>';
                        // <h3>ツイートを指定して変換</h3>
                        // if(isset($_POST['url'])){
                        //     $url = $_POST['url'];
                        //     $urlSprit[] = explode('/', $url);
                        //     $tweetInfo = getTWeet($api_key, $api_secret, $query2["oauth_token"], $query2["oauth_token_secret"], '1413824957830733825');
                        //     list($twJson, $twHeader) = $tweetInfo;
                        //     getTweetTransform($twJson, $twHeader, $igo);
                        // }
                        // <form method="post" action="#" value="ツイートのURLを入力してください">
                        //             <input type="text" name="url">
                        //             <input type="submit">
                        //         </form>
                        print '<h4><img class="delimiterImg" src="imgSvg/glasses.jpg"></img>詳細</h4>
                                <div class="detail">
                                    <p>TLから取得できるツイート数は150件です。150件を超えたツイートは表示されませんので、ご了承ください。</p>
                                    <p>また、このプログラムはMecabと呼ばれる形態素解析エンジンを使用しています。辞書の読み込みが必要なため、ページが重い可能性があります。</p>
                                    <p>オプション機能としてツイートをクリックするとそのツイートから切り出した形態素がリストとして見られるようになっています。</p>
                                    <p>右上のメニューバーにツイートボタンがあるので、ツイートして拡散してくれると嬉しいです。
                                </div>
                                <h4>認証はこちらから↓</h4>
                                <p><a href="https://api.twitter.com/oauth/authorize?oauth_token=' . $query["oauth_token"] . '"><img class="toAuth" src="imgSvg/toAuth.svg" alt="認証画面へ"></img></a></p>
                                
                                <p>使用させていただいたライブラリ：
                                <ul>
                                    <li><a href="https://taku910.github.io/mecab/">MeCab</a>
                                    <li><a href="https://github.com/neologd/mecab-ipadic-neologd">mecab-ipadic-NEologd</a>
                                    <li><a href="https://github.com/rsky/php-mecab.git">php-mecab</a>
                                    <li><a href="https://developer.twitter.com/en/docs/twitter-api">twitter API</a>
                                </ul>
                            </div>';
                    }
                } finally {
                    restore_error_handler();
                }

                ?>
            </div>

        </div>

    </div>

</body>

</html>