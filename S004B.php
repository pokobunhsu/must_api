<?php
session_start();
if($_SESSION['user'] == null){
	echo '<meta http-equiv=REFRESH CONTENT=1;url=./post.php>';
}
$myid = $_SESSION['user'];
$mypw = $_SESSION['pass'];
$hashgo="./cookfile/".substr(md5(rand()),0,50).".txt";
$cookie_jar = $hashgo ;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://sss.must.edu.tw/LoginGo.asp');
curl_setopt($ch, CURLOPT_POST, 1);
$request = "STDNO=".$myid."&PASSWD=".$mypw;

curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
//把返回來的cookie保存在$cookie_jar文件中
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
//設定返回的資料是否自動顯示
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//設定是否顯示頭訊息
curl_setopt($ch, CURLOPT_HEADER, false);
//設定是否輸出頁面內容
curl_setopt($ch, CURLOPT_NOBODY, false);
curl_exec($ch);
curl_close($ch);
//get data after login
$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, 'https://sss.must.edu.tw/qry_smtrscoreTEN97.asp');
curl_setopt($ch2, CURLOPT_HEADER, false);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch2, CURLOPT_COOKIEFILE, $cookie_jar);
$orders = curl_exec($ch2);
$order12 = iconv("Big5", "UTF-8", $orders);

curl_close($ch2);

//str_replace(array("\r","\n","\t","\s"), '', $order12);
$order12 = str_replace(array("\r","\n","\t","\s"), '', $order12);

preg_match_all("/<TD>(.*)<\/TD>/U",$order12,$match);

$i = count($match[0]);

$len = 0;
for($j=1;$j<$i;$j=$j+8)
{	
	$data_array[$len] = [
		strip_tags($match[0][$j])=>[
		"期中"=>strip_tags($match[0][$j+5]),
		"期末"=>strip_tags($match[0][$j+6])]
		];
	$len++;
}
$data_array = json_encode($data_array);
header('Content-Type: application/json; charset=utf-8');
echo $data_array;
?>