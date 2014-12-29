<?php
$taskLocation = realpath(dirname(__FILE__));
chdir($taskLocation);
include ("config.inc.php");
chdir($taskLocation . "/data");
// create a new cURL resource
$ch = curl_init();
// set URL and other appropriate options
curl_setopt($ch, CURLOPT_URL, RASPBERRY_PI);

curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($ch, CURLOPT_HEADER, 0);

curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,60);
// grab URL and pass it to the browser
$line = curl_exec($ch);

// Check for errors and display the error message
if($errno = curl_errno($ch)) {
	die("An error occoured");
}
// close cURL resource, and free up system resources
curl_close($ch);
// Date for folder structure and filename
$fdate = date("Y-m-d");
list($year,$month,$day) = explode("-",$fdate);
if (!is_dir($year)){
	mkdir($year);
}
chdir($year);
if (!is_dir($month)){
	mkdir($month);
}
chdir($month);
$date = date("Y-m-d H:i:s");
$fp = fopen($fdate . '.txt', 'a');
fwrite($fp,$date . "," . $line ."\n");
fclose($fp);

