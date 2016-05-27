<?
//fra http://stackoverflow.com/questions/2510434/php-format-bytes-to-kilobytes-megabytes-gigabytes
function formatBytes($bytes, $precision = 2) { 
    $units = array('bytes', 'kb', 'mb', 'gb', 'tb'); 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 

    // Uncomment one of the following alternatives
    $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow)); 

    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 

error_reporting(E_ALL);
ini_set('display_errors', 1);

$d='../../edderkopper-upload/';
$files=array();
$dir = opendir($d);
//while ($f = readdir($dir)) {
while (false !== ($f = readdir($dir))) {
	//if (!in_array($f, ['.', '..']), true) {

		$file = array();
		$file['name'] = $f;

		$s=filesize($d.$f);
		$size=formatBytes($s,2);
		$file['size'] = $size;

		$time = filemtime($d.$f);
		$file['time'] = $time;

		if ($file['name']!='.' && $file['name']!='..' && $file['name']!='checklist')  {
		   array_push($files, $file); 
        }

	//}

}


function sorter($key) {
    return function($a, $b) use ($key) {
        return strnatcmp($a[$key], $b[$key]);
    };
}
usort($files, sorter('time'));

$count=1;
$html='<table>';
foreach ($files as $file) {
	$html.='<tr>';

	$html.='<td class="csv-name" title="'.$file['name'].'">'.$file['name'].'</td>';
	$html.='<td>&nbsp;'.date('j/m/Y H:i:s', $file['time']).'</td>';
	$html.='<td>&nbsp;'.$file['size'].'</td>';

	$count++;
	
	$html.='<td><button class="csv-check" id="check-'.$count.'">Check</button></td>';
	$html.='<td><button class="csv-replace" id="insert-'.$count.'">Erstat</button></td>';
	$html.='<td><button class="csv-add" id="add-'.$count.'">Tilføj</button></td>';
	$html.='<td><button class="csv-download" id="download-'.$count.'">Download</button></td>';
	$html.='<td><button class="csv-delete" id="delete-'.$count.'">Slet</button></td>';

	$html.='</tr>';
}
$html.='</table>';

echo $html;

?>
