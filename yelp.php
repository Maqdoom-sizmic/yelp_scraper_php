<?php
include('../libs/config.php');
include('../libs/db.php');
include('../libs/simple_html_dom.php');
ini_set('max_execution_time', 900);
connect_db("urls");

ini_set("display_errors", "1");
    error_reporting(E_ALL & ~E_NOTICE);
   error_reporting(E_ALL);

// get outerlinks

$token = "***** IP TOKEN *****"
$opts = array(
    'http'=>array(
        'method'=>"GET",
        "User-Agent:    Mozilla/5.0 (X11; U; SunOS sun4u; en-US; rv:1.9b5) Gecko/2008032620 Firefox/3.0b5",
        
        // 'header' => array(
        //     "Accept-Encoding: gzip",
        //     "Content-Length: totalBytes",
        // ),

        //"Accept-Encoding: gzip",
    )
);
$context = stream_context_create($opts);
$pg_cnt = 20;

for ($i=10; $i <= $pg_cnt; $i+=10) { 
	$page_url = urlencode("https://www.yelp.co.uk/search?find_desc=hotels&find_loc=usa&start=$i");	
	$html = file_get_contents("https://page.rest/fetch?token=$token&url=$page_url&selector=.lemon--a__373c0__IEZFH",  FILE_TEXT, $context);
    return mb_convert_encoding($html, 'UTF-8',mb_detect_encoding($html, 'UTF-8, ISO-8859-1', true));
	$json = json_decode($html, true);
	$res = $json['selectors'];
	echo "<pre>";
	print_r($html);
	foreach ($res as $value) {
	 	foreach ($value as $website) {
	 		$link = "https://www.yelp.com".$website['attrs']['href'];	
	 		if(strpos($link, 'adredir?ad_business_id') != true) {
	 	 		echo $link."<br>";
	 			$ex = $db->get_results("SELECT src_url FROM yelpnonprofit where src_url = '$link'");
	 			if($ex) {
	 				echo "<span style='color:red;'>exists</span><br>";
	 			} else {
 				$db->query("INSERT INTO yelpnonprofit (src_url,status) VALUES ('$link','pending')") ;
	 				// if ($inserted== true) {
	 				// 	echo "khaled";
	 				// }
	 				// else{
	 				// 	echo "not done";
	 				// }
	 				echo "<span style='color:green;'>inserted</span><br>";
	 			}	
	 	 	}									
	 	}			
	 }
}

//get inner links
// $urls = $db->get_results("SELECT * FROM yelpsychic WHERE status = 'pending' limit 233");
// $opts = array(
//     'http'=>array(
//         'method'=>"GET",
//         "User-Agent:    Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6\r\n",
//         "Cookie: foo=bar\r\n"
//     ),
// );
// $context = stream_context_create($opts);

// // print_r($urls);

// foreach ($urls as $line) {

// 	// print_r($line);
// 	// echo "<br>";
// 	// print_r($line->id);
// 	// echo "https://page.rest/fetch?token=$token&url=$line->src_url&selector=.reviewCount__373c0__2r4xT";

// 	$html = file_get_contents("https://page.rest/fetch?token=$token&url=$line->src_url&selector=.biz-website", false, $context);
// 	$json = json_decode($html, true);
// 	$res = $json['selectors'];
// 	echo "<pre>";
// 	// print_r($res);
// 	// $link = $res[".reviewCount__373c0__2r4xT"][0]["text"];
// 	$link = $res[".biz-website"][0]["text"];
// 	// print_r($link);		
// 	$db->query("update yelpsychic set url = '$link' , status = 'completed' where id ='$line->id'");
// 	$db->debug();
// 	echo $link."<span style='color:green;'>inserted</span><br>";
// }

// get json results
// $result = $db->get_results("SELECT DISTINCT url FROM yelpsychic WHERE  status='completed' AND id>235");
// // print_r($result);
// $websites = array();
// 	foreach($result as $line){
// 		if($line->url) {
// 			$websites[] = array("urls"=>array("$line->url"));
// 		}
// 	}
// print_r(json_encode($websites));
?>	
