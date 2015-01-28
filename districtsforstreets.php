<?php
// тут функции, они просто качают из интернета, неважно как, не читать
function postconnect($link,$cookie,$postdata,$opt_header,$opt_follow){
  $сonnection = curl_init();
  curl_setopt($сonnection, CURLOPT_URL,$link);
  curl_setopt($сonnection, CURLOPT_COOKIE,$cookie);
  curl_setopt($сonnection, CURLOPT_HEADER,$opt_header);
  curl_setopt($сonnection, CURLOPT_RETURNTRANSFER,1);
  curl_setopt($сonnection, CURLOPT_POST,1);
  curl_setopt($сonnection, CURLOPT_FOLLOWLOCATION,$opt_follow);  
  curl_setopt($сonnection, CURLOPT_POSTFIELDS, $postdata);
  curl_setopt($сonnection, CURLOPT_сonnectTIMEOUT,30);
  $all = curl_exec($сonnection);
  curl_close($сonnection);
return $all;
}
function getconnect($link,$cookie,$opt_header,$opt_follow){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,$opt_follow);
        curl_setopt($ch, CURLOPT_HEADER,$opt_header);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)");
        curl_setopt($ch, CURLOPT_COOKIE,$cookie);
        $otvet = curl_exec($ch);
        curl_close($ch);
return $otvet;
}
////////////////////////////// начало важного
echo "Starting...\n";

$file = explode("\n", file_get_contents("list.lst"));
$result="";

foreach($file as $string) {
	$street=urlencode("Россия, город Москва, Москва, ".$string);
	$memo = getconnect("http://geocode-maps.yandex.ru/1.x/?callback=jQuery17205788999310649169_1421845731884&geocode=".$street."&results=5&format=json&_=1421845792216",null,null,null);

	$memo=substr($memo,strpos($memo,"{",0), strpos($memo,");",0)-strpos($memo,"{",0));
	$memoarray=json_decode($memo, true);

	$point=$memoarray["response"]["GeoObjectCollection"]["featureMember"][0]["GeoObject"]["Point"]["pos"];
	$point=urlencode(str_replace(" ", ",", $point));
	$impliedstreet=$memoarray["response"]["GeoObjectCollection"]["featureMember"][0]["GeoObject"]["name"];

	$memodist=getconnect("http://geocode-maps.yandex.ru/1.x/?callback=jQuery17205788999310649169_1421845731884&geocode=".$point."&kind=district&results=5&format=json&_=1422431219178",null,null,null);

	$memodist=substr($memodist,strpos($memodist,"{",0), strpos($memodist,");",0)-strpos($memodist,"{",0));
	$memodistarray=json_decode($memodist, true);

	$distrname=$memodistarray["response"]["GeoObjectCollection"]["featureMember"][0]["GeoObject"]["name"];

	$newres=$string.";".$impliedstreet.";".urldecode($point).";".$distrname."\n";
	echo $newres;
	$result.=$newres;
}

// пишем в файл
file_put_contents("result.txt", $result);
?>
