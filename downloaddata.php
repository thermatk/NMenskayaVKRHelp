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
// задаём переменную для записи ответа
$final="";
// цикл для 78 страниц
for($counter=1;$counter<=78;$counter=$counter + 1) {
	// качаем код страницы
	$memo = getconnect("http://mos.memo.ru/shot-".$counter.".htm",null,null,null);
	// просто вывод, чтобы было не скучно
	echo "\nDownloaded page #".$counter."\n";
	// преобразуем в дерево, чтобы использовать библиотеку
	$html = str_get_html($memo);
	// ищем все элементы с названиями улиц	
	$streets = $html->find("p.Street");
	// цикл по найденным улицам
	foreach ($streets as $street) {
		// имя улицы - в переменную
		$name = $street->plaintext;
		// данные о жертвах в соседнем элементе
		$table = $street->next_sibling();
		// цикл по номерам домов
		foreach ($table->find("h4") as $house) {
			// добавляем к результату данные в формате "улица, дом, людей". От людей ещё отрезаем "&nbsp;чел."
			$final.=$name.",".$house->plaintext.",".explode("&nbsp;", $house->next_sibling()->plaintext)[0]."\n";
		}
	}
}
// пишем в файл
file_put_contents("memo.txt", $final);
?>
