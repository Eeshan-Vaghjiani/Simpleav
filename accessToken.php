<?php
//MPESA API KEYS
$consumerKey = "ST53i1u9Hh73gbua6LK66wdYX1umtK7xr7jWvMVTOz0Cv8Yj";

  $consumerSecret = "PgNGBsM19xDnDsfBBA4MlGXY8zLfmG41j6o9A7y21XfUjcj5C9VjBOAZa6IoTaGa";
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


