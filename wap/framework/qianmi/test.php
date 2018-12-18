<?php

/**
 *千米开放平台 PHP调用示例
 *适用于PHP5.1.2及以上版本
 */
header("Content-type:text/html; charset=utf-8");
require("OpenSdk.php");
define('ROOT',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");
    
$loader  = new QmLoader;
$loader  -> autoload_path  = array(CURRENT_FILE_DIR.DS."client");
$loader  -> init();
$loader  -> autoload();

$client  = new OpenClient;
$client  -> appKey =  "10001075";
$client  -> appSecret =  "606f2dcLuDO4KbYedikRJXzVg0IsMRa5";
$accessToken  = "1343917a9a1d4b7eb70c9d56bb413d0e";


$req = new CoachStartStationsListRequest;
$res = $client->execute($req, $accessToken);

print_r(json_encode($res));
file_put_contents(ROOT.'log/get_token.txt',json_encode($res));//保存到本地

?>