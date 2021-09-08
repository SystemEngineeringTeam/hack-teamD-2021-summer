<?php
session_start();
session_regenerate_id(true);
ini_set('memory_limit', '1500M');
?>
<!DOCTYPE HTML>
<html lang="ja">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <title>誰でもメガネをかけた女子中学生</title>
    <link href="./css/utilStyle.css" rel="stylesheet">

</head>

<body>

    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light fixed-top border" style="background-color:#FFFFFF">
            <div class="container-fluid">
                <span class="navbar-text">
                    誰でもメガネをかけた女子中学生
                </span>
                <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-size="large" data-show-count="false" data-url="http://d502-2405-6585-160-5f00-9126-e771-b7fe-e46f.ngrok.io/twitterAPI/hometimeline.php" data-text="">Tweet</a>
                <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
            </div>
        </nav>
    </div>

    <div class="brankHeader"></div>

    <div class="container-fluid pt-3">

        <div class="row justify-content-md-center">

            <div class="col-md-8 col-sm-12 col-xs-12">
                <div class="twAll">
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

    </div>

</body>

</html>