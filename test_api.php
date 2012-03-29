<?php
define(API_KEY,'45a17d84e');
$videoId = $_GET['id'];
$url = "http://magic.glow.cz/api/";
$method = "?method=status&API_KEY=%s&file_id=%s";
		
$api_url = sprintf($url.$method,API_KEY,$videoId);

$curl = curl_init($api_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

curl_setopt ($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt ($curl, CURLOPT_POST, 1);

$response = curl_exec($curl);
$err_no = curl_errno($curl);
$err_msg = curl_error($curl);
curl_close($curl);
echo $response;
exit;

?>