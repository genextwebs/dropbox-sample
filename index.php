<?php
# Include the Dropbox SDK libraries
require_once "lib/Dropbox/autoload.php";
require_once 'examples/helper.php';
use \Dropbox as dbx;

/*$appInfo = dbx\AppInfo::loadFromJsonFile("app-info.json");
$webAuth = new dbx\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");

$authorizeUrl = $webAuth->start();

echo "1. Go to: " . $authorizeUrl . "\n";
echo "2. Click \"Allow\" (you might have to log in first).\n";
echo "3. Copy the authorization code.\n";
$authCode = \trim(\readline("Fhi99ihkXwAAAAAAAAAAEg7KAq3HvCpUIlnVhXLElus"));

list($accessToken, $dropboxUserId) = $webAuth->finish($authCode);
print "Access Token: " . $accessToken . "\n";*/

//generated auth token using dropbox login & using it
$accessToken = 'PUT YOUR TOKEN HERE';

$dbxClient = new dbx\Client($accessToken, "PHP-Example/1.0");
//$accountInfo = $dbxClient->getAccountInfo();
//print_r($accountInfo);


if ($dbxClient === false) {
	header("Location: ".getPath("dropbox-auth-start"));
	exit;
}


//downloading both csvs
$path1 = '/data-a.csv';
$path2 = '/data-b.csv';

downloadFile($dbxClient,$path1);
downloadFile($dbxClient,$path2);

function downloadFile($dbxClient,$path){
	if (!$path) {
		header("Location: ".getPath(""));
		exit;
	}
	
	$fd = tmpfile();
	$metadata = $dbxClient->getFile($path, $fd);
	
	$file_name = str_replace('/','',$path);
	fseek($fd, 0);
	$downloadPath = 'downloads' . $path;
	if(!file_exists($downloadPath))
	file_put_contents($downloadPath,$fd);
	fclose($fd);
}

$row = 1;

$csv1 = 'downloads/data-a.csv';
if (($handle = fopen($csv1, "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 500, ",")) !== FALSE) {
		if(!empty($data[0]))
			$a1[] = $data[0];
		if(!empty($data[2]))
			$a3[] = $data[2];
		if(!empty($data[4]))	
			$a5[] = $data[4];
		if(!empty($data[5]))	
			$a6[] = $data[5];
	}
}

$csv2 = 'downloads/data-b.csv';
if (($handle1 = fopen($csv2, "r")) !== FALSE) {
	while (($data1 = fgetcsv($handle1, 500, ",")) !== FALSE) {
		if(!empty($data1[0]))
			$b1[] = $data1[0];
		if(!empty($data1[2]))
			$b3[] = $data1[2];
		if(!empty($data1[3]))	
			$b4[] = $data1[3];
	}
}


$finalArr = array();
//counting rows of csv
$a1Cnt = count($a1);
for($i=0;$i<$a1Cnt;$i++){
	$finalArr[$i][0] = $a1[$i];
	$finalArr[$i][1] = $a3[$i];	
	$finalArr[$i][2] = $a5[$i];	
	$finalArr[$i][3] = $a6[$i];	
	$finalArr[$i][4] = $b1[$i];	
	$finalArr[$i][5] = $b3[$i];	
	$finalArr[$i][6] = $b4[$i];	
}

function outputCSV($data) {
    $outstream = fopen("php://output", "w");

    function __outputCSV(&$vals, $key, $filehandler) {
        fputcsv($filehandler, $vals); // add parameters if you want
    }

    array_walk($data, "__outputCSV", $outstream);
    fclose($outstream);
}
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=merged_file.csv");
header("Pragma: no-cache");
header("Expires: 0");

outputCSV($finalArr);