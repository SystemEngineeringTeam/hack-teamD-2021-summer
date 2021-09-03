<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>Twitter REST API OAuth接続 ツイート投稿[ POST statuses/update.json ] | WEPICKS!</title>
</head>
<body>
 
<h1>Twitter REST API OAuth接続 ツイート投稿[ POST statuses/update.json ]</h1>
 
<?php
#########################################
### ツイートの投稿
if(!empty($_POST['tweet'])){
 
 //tmhOAuth.phpをインクルードします。
 require_once("./tmhOAuth.php");
 
 //Access Tokenの設定 apps.twitter.com でご確認下さい。
 //Consumer keyの値を格納
$sConsumerKey = "KFYqe6m5v6BMw3nt5U9lQtfIX";
//Consumer secretの値を格納
$sConsumerSecret = "5vIVRCjtOSA3RD9rWCTzV7CDYifnTzVLtMO26IvPR41TiVQC6B";
//Access Tokenの値を格納
$sAccessToken = "1272371880595296256-ot0XuuYwLgsjQO8sDAaedqMsQPAAGb";
//Access Token Secretの値を格納
$sAccessTokenSecret = "SzwF0VdpdsmZiPqG7g9YW7Jg8661Gc71GJY7XEwfLvJuM";
 
 //OAuthオブジェクトを生成する
 $twObj = new tmhOauth(
 array(
 "consumer_key" => $sConsumerKey,
 "consumer_secret" => $sConsumerSecret,
 "token" => $sAccessToken,
 "secret" => $sAccessTokenSecret,
 "curl_ssl_verifypeer" => false,
 )
 );
 
 //ツイート内容
 $sTweet = $_POST['tweet'];
 
 //Twitter REST API 呼び出し
 $code = $twObj->request( 'POST', "https://api.twitter.com/1.1/statuses/update.json",array("status" => $sTweet));
 
 // statuses/update.json の結果をjson文字列で受け取り配列に格納
 $aResData = json_decode($twObj->response["response"], true);
 
 //配列を展開
 if(isset($aResData['errors']) && $aResData['errors'] != ''){
 ?>
 ツイート投稿に失敗しました。<br/>
 エラー内容：<br/>
 <pre>
 <?php var_dump($aResData); ?>
 </pre>
 <?php
 }else{
 //配列を展開
 $iTweetId =                 $aResData['id'];
 $sIdStr =                   (string)$aResData['id_str'];
 $sText=                     $aResData['text'];
 $sName=                     $aResData['user']['name'];
 $sScreenName=               $aResData['user']['screen_name'];
 $sProfileImageUrl =         $aResData['user']['profile_image_url'];
 $sCreatedAt =               $aResData['created_at'];
 $sStrtotime=                strtotime($sCreatedAt);
 $sCreatedAt =               date('Y-m-d H:i:s', $sStrtotime);
 ?>
 <h3><?php echo $sName; ?> へツイート投稿</h3>
 <a href="https://twitter.com/<?php echo $sScreenName; ?>" target="_blank" rel="nofollow"><?php echo $sScreenName; ?></a>
 <ul>
 <li>IDNO[id] : <?php echo $iTweetId; ?></li>
 <li>名前[name] : <?php echo $sIdStr; ?></li>
 <li>スクリーンネーム[screen_name] : <?php echo $sScreenName; ?></li>
 <li>プロフィール画像[profile_image_url] : <img src="<?php echo $sProfileImageUrl; ?>" /></li>
 <li>つぶやき[text] : <?php echo $sText; ?></li>
 <li>ツイートタイム[created_at] : <?php echo $sCreatedAt; ?></li>
 </ul>
 <?php
 }
}
?>
 
<?php
#########################################
### 投稿フォーム
?>
 
<h1>投稿フォーム</h1>
<form action="tweetpost.php" method="POST">
ツイート：<br>
<textarea name="tweet" readonly="readonly"><?php echo "呟きのTEST投稿です。 (".date('Y-m-d H:i:s').")";?></textarea><br>
<input type="submit" value="送信" />
</form>
 
</body>
</html>