<?php
require('function.php');
require('twitteroauth/autoload.php');//https://twitteroauth.com/これ参考ん試す
use Abraham\TwitterOAuth\TwitterOAuth;

$CONSUMER_KEY = "lIaLoYXS2ojQrrBumQm3BAHJm";
$CONSUMER_SECRET = "EvzKr04xp0fZ4cbrPPKKjrhLOQuHlJJ3Fqw0wwKZW3crkKUsJL";
$access_token = "1163033289487032320-79HzbvgSxs4MR90fxCn8gNSBCtlmdu";
$access_token_secret = "NsIZpHPyJh0SNlpdGSBzu7kvI20XLXXcTa26s4W5nLBYw";
$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $access_token, $access_token_secret);

$statuses = $connection->get("search/tweets", ["q" => "モンキーバレルズ",'count' => '10']);
$array = json_decode(json_encode($statuses), true);
foreach ($array as $value) {
  print_r($value);
}

//$tw_results = json_decode(json_encode($statuses), true);



//$results = json_encode($statuses, true);
//print_r($array);
//$tw_results = json_decode(json_encode($statuses), true);
//$results = json_encode($statuses, true);

//print_r($tw_results);
