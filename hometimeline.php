<?php
session_start();
session_regenerate_id(true);
ini_set('memory_limit', '1500M');
?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>Twitter REST API OAuth接続 ホームタイムライン取得[ GET statuses/home_timeline.json ] | WEPICKS!</title>
</head>
<body>
 
<h1>Twitter REST API OAuth接続 ホームタイムライン取得[ GET statuses/home_timeline.json ]</h1>
 
<?php
require_once("./util.php");
require_once("igo-php-0.1.7/lib/Igo.php");

$igo = new Igo('ipadic', 'UTF-8');

$api_key = "KFYqe6m5v6BMw3nt5U9lQtfIX";
$api_secret = "5vIVRCjtOSA3RD9rWCTzV7CDYifnTzVLtMO26IvPR41TiVQC6B";
$callback_url = "http://127.0.0.1/twitterAPI/hometimeline.php";

$_SESSION["oauth_token_secret"] = '';

if(isset($_GET["oauth_token"]) || isset($_GET["oauth_verifier"])){
    $query = getAccessToken($api_key, $api_secret);
    $tweetInfo = getHomeTimeLine($api_key, $api_secret, $query["oauth_token"], $query["oauth_token_secret"]);
    list($json, $header) = $tweetInfo;

    $aResData = json_decode($json, true);
    for($iTweet = 0; $iTweet < sizeof($aResData); $iTweet++){
        $iTweetId =                 $aResData[$iTweet]['id'];
        $sIdStr =                   (string)$aResData[$iTweet]['id_str'];
        $sText=                     $aResData[$iTweet]['full_text'];
        $sName=                     $aResData[$iTweet]['user']['name'];
        $sScreenName=               $aResData[$iTweet]['user']['screen_name'];
        $sProfileImageUrl =         $aResData[$iTweet]['user']['profile_image_url'];
        $sCreatedAt =               $aResData[$iTweet]['created_at'];
        $sStrtotime=                strtotime($sCreatedAt);
        $sCreatedAt =               date('Y-m-d H:i:s', $sStrtotime);
        $text = '';

        print '<img src='.$sProfileImageUrl.'>';
        print '<h2>'.$sName.'(@'.$sScreenName.')さんのつぶやき</h2>';
        print $sText.'<br>';
        foreach(transText($sText, $igo) as $value){
            $feature = explode(',', $value->feature);

            print 'surface=>'.$value->surface.'<br>';
            print 'feature=>'.$value->feature.'<br>';
            // if(strcmp($value->$surface, 'きみたち'))
            // if(strcmp($feature[0], '名詞') == 0){
                
            // }
            print '<br>';
        }
        print '<hr>';
        print '<br>';
        // $sText = transText($sText, $igo);
        // $html .= '<p>'.$sText.'</p>';
        // $html .= '<hr>';
    }
    // アプリケーション連携の解除
    $html = '';
    $html .= '<h2 style="color:red">アプリケーション連携の解除</h2>' ;
    $html .= '<p>このアプリケーションとの連携を解除するには、下記ページより、行なって下さい。</p>' ;
    $html .= '<p><a href="https://twitter.com/settings/applications" target="_blank">https://twitter.com/settings/applications</a></p>' ;

    print $html;
    
    exit();
}else if(isset($_GET["denied"])){
    print "<p>連携が拒否されました。</p>";
    exit();
}else{
    $query = getRequestToken($api_key, $api_secret, $callback_url);
    print '<p><a href="https://api.twitter.com/oauth/authorize?oauth_token='.$query["oauth_token"].'">認証画面へ</a></p>';
}
?>