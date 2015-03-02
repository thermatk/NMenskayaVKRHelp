<?php
// это библиотека, которая по html ходит
include('simple_html_dom.php');
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

function grab($inf,$begin,$end){
	if (substr_count($inf, $begin) and substr_count($inf, $end)){
		$begin=strpos($inf,$begin)+strlen($begin);
		$end=strpos($inf,$end,$begin);
		$grab=substr($inf,$begin, $end-$begin);
		return $grab;
	} else {
		return FALSE;
	}
}
////////////////////////////// начало важного
echo "Starting...\n";


$file = explode("\n", file_get_contents("result.txt"));
$final="";

foreach($file as $string) {
	if($string=="") {
		break;	
	}
	$data = explode(";", $string);

	if($data[1]!="false") {
	
		$page = getconnect("http://dom.mos.ru".$data[1]);
		// просто вывод, чтобы было не скучно
		echo "\nDownloaded page for ".$data[0];
		// преобразуем в дерево, чтобы использовать библиотеку
		$html = str_get_html($page);
		$divtsg = $html->find("div.padAll1 p");
		$tsg="";
		if(isset($divtsg[0])) {
			$tsg = str_replace (";", "",str_replace (" ", "",str_replace ("\n", "", grab($divtsg[0]->innertext,"<br>","<br>"))));	
		} else {
			$tsg = "другое";		
		}
		$table = $html->find("table.infoCompanyTable tbody")[0];
		$tablestr = str_replace (" ", "", $table -> innertext);
		$buildyear = grab($tablestr, "постройки:</td><td>", "</td>");
		$seriya = str_replace (";", "",grab($tablestr, "Серия</td><td>", "</td>"));
		$etazh = grab($tablestr, "Этажность:</td><td>", "</td>");
		$ploschad = str_replace (";", "",grab($tablestr, "Общаяплощадь:</td><td>", "</td>"));
		$gilploschad = str_replace (";", "",grab($tablestr, "площадьжилыхпомещений:</td><td>", "</td>"));
		$final.= $data[0].";".$tsg.";".$buildyear.";".$seriya.";".$etazh.";".$ploschad.";".$gilploschad."\n";
	} else {
		$final.= $data[0].";false\n";	
	}
}
// пишем в файл
file_put_contents("datafinal.txt", $final);
?>
