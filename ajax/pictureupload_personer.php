<?

$action=(isset($_GET['action'])) ? $_GET['action'] : false;

if ($action) switch($action) {
	case 'get' :
		echo file_get_contents('../pictureupload/personer.txt');
		break;
	case 'put' :
		$text=$_GET['text'];
		echo $text;
		$text=explode("\n", $text);
		$newText='';
		foreach($text as $line) {
			//$line=preg_replace('/[\x00-\x1F\x7F]/', '', $line);
			if (ltrim(rtrim($line))!=='') $newText.=$line."\n";
		}
		file_put_contents('../pictureupload/personer.txt', $newText);
		break;
	default :
		break;
}

?>

		
		

