<?php
//MPESA API KEYS
$consumerKey = "ksiGnEyhUcoTK2m4Z11fOdwhhI3AEUXZ6HGhbecUokhKj4cG";

  $consumerSecret = "GLBLdTnBhNXDoGBkAmWFI8sBHFNQTD7jOUplADwfb18bRgFGZPbj934cych1l8sk";
//ACCESS TOKEN URL
$access_token = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
$headers = ['Content-Type:application/json; charset=utf8'];
$curl = curl_init($access_token);
curl_setopt($curl , CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl , CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl , CURLOPT_HEADER, FALSE);
curl_setopt($curl , CURLOPT_USERPWD , $consumerKey . ':' . $consumerSecret);
$result = curl_exec($curl);
$status = curl_getinfo($curl , CURLINFO_HTTP_CODE);

$result = json_decode($result);
echo $access_token =$result  -> access_token;
curl_close($curl);


