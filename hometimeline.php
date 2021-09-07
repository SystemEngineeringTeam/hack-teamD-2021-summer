<?php
session_start();
session_regenerate_id(true);
ini_set('memory_limit', '1500M');
?>
<!DOCTYPE HTML>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <title>誰でもメガネをかけた女子中学生</title>
    <link href="./css/utilStyle.css" rel="stylesheet">
</head>

<body>

    <div class="container-fluid">

        <div class="row justify-content-md-center">

            <div class="col"></div>

            <div class="col">

                <h1>誰でもメガネをかけた女子中学生</h1>
                <?php
                require_once("./util.php");
                require_once("igo-php-0.1.7/lib/Igo.php");
                $_SESSION["oauth_token_secret"] = '';

                $igo = new Igo('ipadic', 'UTF-8');

                $api_key = "KFYqe6m5v6BMw3nt5U9lQtfIX";
                $api_secret = "5vIVRCjtOSA3RD9rWCTzV7CDYifnTzVLtMO26IvPR41TiVQC6B";


                if ((!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
                    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
                    (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443')
                ) {
                    $server_request_scheme = 'https';
                } else {
                    $server_request_scheme = 'http';
                }
                //httpかhttpsかの判定がこれで出来るはずだがngrokで試してる範疇では何故かどっちにしてもhttpしか返してくれなかった

                $thisUrl = $server_request_scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

                $callback_url = $thisUrl;

                if (isset($_GET["oauth_token"]) || isset($_GET["oauth_verifier"])) {
                    if(0){
                        print "<a href=" . $callback_url . ">更新</a>";
                        //http://7448-2405-6585-160-5f00-9126-e771-b7fe-e46f.ngrok.io/twitterAPI/hometimeline.php?oauth_token=9M5kvAAAAAABS_BQAAABe7-y6LI&oauth_verifier=GY8nGxBMJMbCujeRbPP7y1jEE2EMYz04
                        //http://7448-2405-6585-160-5f00-9126-e771-b7fe-e46f.ngrok.io/twitterAPI/hometimeline.php?oauth_token=9M5kvAAAAAABS_BQAAABe7-y6LI&oauth_verifier=GY8nGxBMJMbCujeRbPP7y1jEE2EMYz04
                    }
                    $query = getAccessToken($api_key, $api_secret);
                    $tweetInfo = getHomeTimeLine($api_key, $api_secret, $query["oauth_token"], $query["oauth_token_secret"]);
                    list($json, $header) = $tweetInfo;

                    getTimeLineTransform($json, $header, $igo);
                } else if (isset($_GET["denied"])) {
                    print "<p>連携が拒否されました。</p>";
                    exit();
                } else {
                    $query = getRequestToken($api_key, $api_secret, $callback_url);
                    print 'TLの人たちがメガネをかけた女子中学生になります。';
                    print '<p><a href="https://api.twitter.com/oauth/authorize?oauth_token=' . $query["oauth_token"] . '">認証画面へ</a></p>';
                }
                ?>
            </div>

        </div>

    </div>

</body>

</html>