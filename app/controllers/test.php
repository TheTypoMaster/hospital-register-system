<?php

$curl_request = curl_init();

$paras = [
    'grant_type' => 'client_credential',
    'appid' => 'wx8a975fb1e046442e',
    'secret' => '3698bdf9bdcf8cffeb1d0c87953503f7'
];

$url = 'https://api.weixin.qq.com/cgi-bin/token?'.http_build_query( $paras );

curl_setopt( $curl_request, CURLOPT_URL, $url );

$response = curl_exec( $curl_request );

curl_close( $curl_request );

echo $response;