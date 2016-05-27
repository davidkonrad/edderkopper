<!doctype html>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
</head>
<body>
<?

include('../common/Db.php');

class Table extends Db {
	
	public function __construct() {
		parent::__construct();
	}

	public function insert($nr, $navn, $h_nr, $year1, $f_nr, $year2, $r_nr, $areal) {
		$SQL='insert into Natura2000 set '.
			'nr='.$this->q($nr).
			'navn='.$this->q($navn).
			'h_nr='.$this->q($h_nr).
			'aar_for_aendringer_1='.$this->q($year1).
			'f_nr='.$this->q($f_nr).
			'aar_for_aendringer_2='.$this->q($year2).
			'r_nr='.$this->q($nr).
			'areal_ha='.$this->q($areal, false);

		echo $SQL;
		$this->exec($SQL);
	}
}

$table = new Table();

$json = file_get_contents('habitater_27062013.json');
$orgdata = json_decode($json, true);

/*
echo count($orgdata);
foreach ($orgdata['habitater'] as $data) {
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}
*/

function getHabitatByName($name) {
	$result=array();
	foreach ($orgdata['habitater'] as $data) {
		if ($data['navn']==$name) {
			$result[]=$data;
		}
	}
	return $result;
}
	
	

$content = file_get_contents('Natura2000nr.htm');

$DOM = new DOMDocument;
$DOM->loadHTML($content);

$items = $DOM->getElementsByTagName('tr');

$cols = array('Natura2000 nr.', 'Navn', 'H-nr.', 'År for ændringer', 'F-nr.', 'År for ændringer', 'R-nr', 'Areal ha');

for ($i = 0; $i < $items->length; $i++) {
	/*
	echo '<pre>';
	print_r($items->item($i)->nodeValue);
	echo '</pre>';
	*/
	//echo $items->item($i)->nodeValue . "<br/>";
	$node=$items->item($i);

	$index=0;
	$array=array();

	foreach($node->childNodes as $child) {
		if ($child->nodeName=='td') {
			//"Areal ha" er i visse tilfælde på pos "8", eg 7
			if (!isset($cols[$index])) {
				//echo $cols[$index-1].' -> '.$child->nodeValue.'<br>';			
				$array[]=$child->nodeValue;
			} else {
				//echo $cols[$index].' -> '.$child->nodeValue.'<br>';			
				$array[]=$child->nodeValue;
			}
			$index=$index+1;
		}
    }
	//echo '<br><br>';
	$table->insert($array[0], $array[1], $array[2], $array[3], $array[4], $array[5], $array[6], $array[7]);
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}

?>
