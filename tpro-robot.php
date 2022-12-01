<?php 

$connect = mysqli_connect("*", "*", "*", "*");
$connect->set_charset('utf8mb4');

$time = date('Y-m-d H:i:s');

$ch = curl_init();
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0");   
curl_setopt($ch, CURLOPT_COOKIEJAR, str_replace("\\", "/", getcwd()).'/gearbest.txt'); 
curl_setopt($ch, CURLOPT_COOKIEFILE, str_replace("\\", "/", getcwd()).'/gearbest.txt'); 
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_URL, 'https://tpro.by/personal/?login=yes');
curl_setopt($ch, CURLOPT_POSTFIELDS,  array(
  'AUTH_FORM' => "Y",
  'TYPE' => "AUTH",
  'backurl' => '\/personal\/',
  'USER_REMEMBER' => "1",
  'USER_LOGIN' => "*", 
  'USER_PASSWORD' => "*"
));
curl_setopt($ch, CURLOPT_URL, 'https://tpro.by/personal/cart/'); 
curl_setopt($ch, CURLOPT_HEADER, 0); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response['html'] = curl_exec($ch);
$info = curl_getinfo($ch);
if($info['http_code'] != 200 && $info['http_code'] != 404) {
$error_page[] = array(1, $page_url, $info['http_code']);
}
$response['code'] = $info['http_code'];
$response['errors'] = $error_page;
curl_close($ch);

preg_match_all('#<table class=\"productTable\">(.+)<\/div>\s*?<div class=\"orderLine\">#su', $response['html'], $res);
preg_match_all('#<tr[^>]*?>(.*?)<\/tr>#su', $res[0][0], $res2);

foreach($res2[0] as $key => $value) {
  
  if ($key > 0) {
    
    preg_match_all('#<a[^>]+?href=\"(.+?)\"[^>]*?>#su', $value, $href);
    preg_match_all('#<a[^>]+?class\s*?=\s*?\"name\"[^>]*?>(.+?)<\/a>#su', $value, $name);
    preg_match_all('#<input[^>]+?data-max-quantity=\"(.+?)\"[^>]*?>#su', $value, $quantity);
    preg_match_all('#<span[^>]+?class\s*?=\s*?\"priceContainer\"[^>]*?>(.+?) руб.<\/span>#su', $value, $price);
    
    $ename = $name[1][0];
    $equantity = $quantity[1][0];
    $eprice = str_replace(" ", '', $price[1][0]);

    $query = "INSERT INTO pars_tpro (name, quantity, price, date) VALUES ('$ename', '$equantity', '$eprice', '$time')";
    mysqli_query($connect, $query);
    
  }
  
}

echo 'tpro pars succeful'

?>