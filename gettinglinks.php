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
function getconnect($link){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)");
        $otvet = curl_exec($ch);
        curl_close($ch);
return $otvet;
}
////////////////////////////// начало важного
echo "Starting...\n";

$file = explode("\n", file_get_contents("list2.txt"));
$result="";
foreach($file as $string) {
	if($string=="") {
		break;	
	}
	echo "\nDoing request ".$string."\n";
	$street=urlencode($string);
	$memo = getconnect("http://dom.mos.ru/Lookups/GetAddressAutoComplete?term=".$street);
	
	$memoarray=json_decode($memo, true);
	$found = false;
	foreach($memoarray as $oneresult) {
		if(substr_count($oneresult['value'], $string) && !$found) {
			$found = true;

			$newres=$string.";".$oneresult['url']."\n";
			$result.=$newres;	
		}
	}
	if(!$found) {
		$newres=$string.";false\n";
		$result.=$newres;	
	}
}

// пишем в файл
file_put_contents("result.txt", $result);
?>
