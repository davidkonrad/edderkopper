<?php

if (isset($_GET['sample'])) {
	$filename = date('YmdHis').'sample.jpg';
} else {
	$filename = date('YmdHis').'.jpg';
}
$from = (isset($_GET['file'])) ? $_GET['file'] : '';

$server='..';
$dir = '/digitalisering/uploads/';

if (isset($_GET['project_id'])) $dir.=$_GET['project_id'].'/';

//$url = 'http://' . $_SERVER['HTTP_HOST'] . '/'. $dir. '/' . $filename;
$url = $server.$dir.$filename;

if ($from=='') {
	//$result = file_put_contents( $filename, file_get_contents('php://input') );
	$result = file_put_contents($url, file_get_contents('php://input'));
	chmod($url, 0777);

	if (!$result) {
		echo "fejl";
		exit();
	}

	//$dir = dirname($_SERVER['REQUEST_URI']);
	//$dir = 'webcam-pictures';
	$host = $_SERVER["SERVER_ADDR"]; 
	if (($host=='127.0.0.1') || ($host=='::1')) {
		$dir='/samlinger'.$dir;
	}

	$url = 'http://' . $_SERVER['HTTP_HOST'] . $dir. $filename;
	print "$url\n";
} else {
	$name=pathinfo($from);
	$dir=$_GET['project_id'];
	$name=$name['basename'];
	//echo $name.' '.$filename;
	rename('uploads/'.$dir.'/'.$name, 'uploads/'.$dir.'/'.$filename);
	chmod('uploads/'.$dir.'/'.$filename, 0777);
	print $filename;
}

?>
