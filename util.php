<?php
function getAccessToken($api_key, $api_secret){
    $request_token_secret = $_SESSION["oauth_token_secret"];
    $request_url = "https://api.twitter.com/oauth/access_token";
    $request_method = "POST";
    $signature_key = rawurlencode($api_secret)."&".rawurlencode($request_token_secret);

	$params = array(
		"oauth_consumer_key" => $api_key ,
		"oauth_token" => $_GET["oauth_token"] ,
		"oauth_signature_method" => "HMAC-SHA1" ,
		"oauth_timestamp" => time() ,
		"oauth_verifier" => $_GET["oauth_verifier"] ,
		"oauth_nonce" => microtime() ,
		"oauth_version" => "1.0" ,
    ) ;
    $p = array();

	foreach($params as $key => $value){
        $params[$key] = rawurlencode($value);
    }

    ksort($params);

    list($response, $header) = getSignature($params, $request_method, $request_url, $signature_key, false, $p);

    $query = [] ;
    parse_str( $response, $query ) ;
    
    return $query;
}

function getRequestToken($api_key, $api_secret, $callback_url){
    $access_token_secret = "";
    $request_url = "https://api.twitter.com/oauth/request_token";

    $request_method = "POST";
    $signature_key = rawurlencode($api_secret)."&".rawurlencode($access_token_secret);

    $params = array(
        "oauth_callback" => $callback_url,
        "oauth_consumer_key" => $api_key,
        "oauth_signature_method" => "HMAC-SHA1",
        "oauth_timestamp" => time(),
        "oauth_nonce" => microtime(),
        "oauth_version" => "1.0",
    );
    $p = array();

    foreach($params as $key => $value){
        if($key == "oauth_callback"){
            continue;
        }

        $params[$key] = rawurlencode($value);
    }

    ksort($params);

    list($response, $header) = getSignature($params, $request_method, $request_url, $signature_key, false, $p);


    if(!$response){
        print "<p>リクエストトークンを取得できませんでした。$api_keyと$callback_url、そしてTwitterのアプリケーションに設定しているCallback URLを確認して下さい。</p>";
        exit();
    }

    $query = [];
    parse_str($response, $query);

    $_SESSION["oauth_token_secret"] = $query["oauth_token_secret"];

    return $query;
}

function getHomeTimeLine($api_key, $api_secret, $access_token, $access_token_secret){
    $request_url = 'https://api.twitter.com/1.1/statuses/home_timeline.json';
    $request_method = 'GET';

    $params_a = array(
        'count' => 150,
        'exclude_replies' => true,
        'include_entities' => true,
        'include_rts' => false,
        'tweet_mode' => 'extended',
    );

    $signature_key = rawurlencode($api_secret).'&'.rawurlencode($access_token_secret);

    $params_b = array(
        'oauth_token' => $access_token,
        'oauth_consumer_key' => $api_key,
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_timestamp' => time(),
        'oauth_nonce' => microtime(),
        'oauth_version' => '1.0',
    );

    $params_c = array_merge($params_a, $params_b);
    ksort($params_c);
    list($json, $header) = getSignature($params_c, $request_method, $request_url, $signature_key, true, $params_a);

    return [$json, $header];
}

function getSignature($params, $request_method, $request_url, $signature_key, $isGetTwi, $params_a){
    $request_params = http_build_query($params, "", "&");
    if($isGetTwi){ $request_params = str_replace( array( '+' , '%7E' ) , array( '%20' , '~' ) , $request_params ) ;}
    $request_params = rawurlencode($request_params);
    $encoded_request_method = rawurlencode($request_method);
    $encoded_request_url = rawurlencode($request_url);
    $signature_data = $encoded_request_method."&".$encoded_request_url."&".$request_params;

    $hash = hash_hmac("sha1", $signature_data, $signature_key, TRUE);
    $signature = base64_encode($hash);

    $params["oauth_signature"] = $signature;
    $header_params = http_build_query($params, "", ",");

    $context = array(
	    "http" => array(
		    "method" => $request_method , // リクエストメソッド (POST)
		    "header" => array(			  // カスタムヘッダー
			    "Authorization: OAuth ".$header_params ,
		    ) ,
	    ) ,
    ) ;

    if($isGetTwi && $params_a){
        $request_url .= '?'.http_build_query($params_a);
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $request_url);
    curl_setopt($curl, CURLOPT_HEADER, true );
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $context["http"]["method"]);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $context["http"]["header"]);
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);
    $res1 = curl_exec($curl);
    $res2 = curl_getinfo($curl);
    curl_close($curl);

    $response = substr($res1, $res2["header_size"]);
    $header = substr($res1, 0, $res2["header_size"]);

    return [$response, $header];
}

function transText($text, $igo){
    $response = $igo->parse($text);
    
    return $response;
}

function getTimeLineTransform($json, $header, $igo){
    $aResData = json_decode($json, true);
    for($iTweet = 0; $iTweet < sizeof($aResData); $iTweet++){
        $iTweetId =                 $aResData[$iTweet]['id'];
        $sIdStr =                   (string)$aResData[$iTweet]['id_str'];
        $sText=                     $aResData[$iTweet]['full_text'];
        $sName=                     $aResData[$iTweet]['user']['name'];
        $sScreenName=               $aResData[$iTweet]['user']['screen_name'];
        $sProfileImageUrl =         $aResData[$iTweet]['user']['profile_image_url'];
        $sCreatedAt =               $aResData[$iTweet]['created_at'];
        $sStrtotime =               strtotime($sCreatedAt);
        $sCreatedAt =               date('Y-m-d H:i:s', $sStrtotime);
        $splitText = transText($sText, $igo);

        // $sText = preg_replace('\#.+\s', "", $sText);
        // $sText = str_replace()
        print '<img src='.$sProfileImageUrl.'>';
        print '<h2>メガネをかけている女子中学生である'.$sName.'(@'.$sScreenName.')さんのつぶやき</h2>';
        print $sText;
        $transedText = array();
        foreach($splitText as $value){
            $feature = explode(',', $value->feature);
            print 'surface=>'.$value->surface.'<br>';
            print 'feature=>'.$value->feature.'<br>';
            if(strcmp(end($splitText)->surface, 'た') == 0 && strcmp($feature[0], '助動詞') == 0 && strcmp($value->surface, 'た') == 0){
                array_push($transedText, str_replace('た', 'たんだがwwwwwwwwww', $value->surface));
                continue;
            }
            if(strcmp($feature[0], '助動詞') == 0 && strcmp($value->surface, 'です') == 0){
                array_push($transedText, str_replace('です', 'です。うん。', $value->surface));
                continue;
            }
            if(strcmp($feature[0], '助動詞') == 0 && strcmp($value->surface, 'ます') == 0){
                array_push($transedText, str_replace('ます', 'ます。うん。はい。', $value->surface));
                continue;
            }
            if(strcmp($feature[0], '名詞') == 0 && strcmp($value->surface, '笑') == 0){
                array_push($transedText, str_replace('笑', 'wwwwwwwww', $value->surface));
                continue;
            }
            array_push($transedText, $value->surface);
        }
        if(mb_substr_count($sText, '!') >= 1 || mb_substr_count($sText, '！') >= 1){
            array_unshift($transedText, 'オイイイイイィィィィィィィィィィ！！！！！！！！！！');
        }
        print implode($transedText);
        
        print '<hr>';
        print '<br>';
    }
    // アプリケーション連携の解除
    $html = '';
    $html .= '<h2 style="color:red">アプリケーション連携の解除</h2>' ;
    $html .= '<p>このアプリケーションとの連携を解除するには、下記ページより、行なって下さい。</p>' ;
    $html .= '<p><a href="https://twitter.com/settings/applications" target="_blank">https://twitter.com/settings/applications</a></p>' ;

    print $html;
    
    exit();
}
?>