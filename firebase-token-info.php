<?php

define('firebaseMessagingToken','AAAAfKVBkmc:APA91bH1YupHy-dAJSyT8aVhDbVQAnlqdjkX3qIMSltxpzM1W8qSDNG2g6Noz4z-wN2rh6jcN2XTMqYTLB33MhjxdqQ-BnU3yjfkQ5F_SzL0H8--JMvjkvRd_vinW5L4bz6zfwz7gmj3');

function getInfoToken(){
    //$token = "fxlh6WxZ2Ws:APA91bF4F-djvEZZ2OjG8Kx31Uc3NY-vdXc7LSxkYJARwfovBj1DuV-hoZnx28if53DIigSSbqxr05F2mxnViRHHLHj6488BWUMu66RlmZyDyflEprMZ09Dsp_WrC0l569djKxgn6TRl";
    $token = "fE_tUC8CEKs:APA91bH6JCfdSjYxr6aIqRe9m4vyj9z_6iYQf00grf4emulrUBgLpYB8pVHk1Toz4-82ZIIFsKBugf9V6QkrRiOI0zZXOjVbO1RkxcvaC16ZbqEyCmTcz9UUt06nkf93Me1TtyYZ3CzO";

    $url = "https://iid.googleapis.com/iid/info/".$token."?details=true";
    $ch = curl_init($url);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key='.firebaseMessagingToken;
  
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);      
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    echo $response;

    echo " code: ".$info['http_code'];
}

getInfoToken();
