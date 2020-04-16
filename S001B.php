<?php
session_start();
if($_SESSION['user'] == null){
	echo '<meta http-equiv=REFRESH CONTENT=1;url=./post.php>';
}
$myid = $_SESSION['user'];
$mypw = $_SESSION['pass'];
$hashgo="./cookfile/".$myid.".txt";
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
curl_setopt($ch2, CURLOPT_URL, 'https://sss.must.edu.tw/qry_stdbasic.asp');
curl_setopt($ch2, CURLOPT_HEADER, false);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch2, CURLOPT_COOKIEFILE, $cookie_jar);
$orders = curl_exec($ch2);
$order12 = iconv("Big5", "UTF-8", $orders);

curl_close($ch2);

//echo str_replace(array("\r","\n","\t","\s"), '', $order12);
$order12 = str_replace(array("\r","\n","\t","\s",'<br>'), '', $order12);

preg_match_all("/<TD.*>(.*)<\/TD>/U",$order12,$match);

$i = count($match[0]);
$photo= str_replace(array("\r","\n","\t","\s",'</TD>','<TD ROWSPAN=4 VALIGN=CENTER>','<br>',"<img src='","'width=200 high=230>"), '', $match[0][2]);
$photo= str_replace(array("photo/"), "https://sss.must.edu.tw/photo/", $photo);

$url = $photo;
$refer = 'https://sss.must.edu.tw/qry_stdbasic.asp';
$ch = curl_init();
//以url的形式 進行請求
curl_setopt($ch, CURLOPT_URL, $url);
//以檔案流的形式 進行返回不直接輸出到瀏覽器
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//瀏覽器發起請求 超時設定
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
//偽造來源地址 
curl_setopt ($ch, CURLOPT_REFERER, $refer);
$file = curl_exec($ch);
curl_close($ch);
header('Content-Type: text/html');
// 對圖片進行base64編碼，然後返回給前端展示
$file = base64_encode($file);

//echo "<img src='data:image/jpeg;base64,{$file}' width=200 high=230 >";//學生照片

$data_array = [
"photo"=>$file,
"學號"=>strip_tags($match[0][4]),
"姓名"=>strip_tags($match[0][6]),
"身分證字號"=>strip_tags($match[0][10]),
"生日"=>strip_tags($match[0][12]),
"系別班級"=>strip_tags($match[0][16]),
"入學方式"=>strip_tags($match[0][18]),
"電子郵件"=>strip_tags($match[0][20]),
"通訊地址"=>strip_tags($match[0][22]),
"戶籍地址"=>strip_tags($match[0][24]),
"監護人："=>strip_tags($match[0][26]),
"機車牌照"=>strip_tags($match[0][32]),
"通訊電話"=>strip_tags($match[0][38]),
];

$data_array = json_encode($data_array);
header('Content-Type: application/json; charset=utf-8');
echo $data_array;

?>