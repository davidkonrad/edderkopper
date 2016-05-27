<!doctype html>
<html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="http://localhost/samlinger/plugins/jquery.xml2json.js"></script>
</head>
<body>

<div id="output">
</div>
<div id="script">
</div>

<?
$habitatNames = array();

function loadHabitatTab() {
	global $habitatNames;
	if (($handle = fopen('habitater.tab', "r")) !== false) {
		$fieldNames = fgetcsv($handle, 1000, ';');
		while (($record = fgetcsv($handle, 1000, ';')) !== false) {
			$array = array();
			$index = 0;
			$habitatNames[]=array($record[0], $record[1]);
		}
	}
	echo '<pre>';
	//print_r($habitatNames);
	echo '</pre>';
}

function processCoords($coords) {
	$result=array();
	$c = explode(' ',$coords);
	foreach ($c as $coord) {
		$coord=str_replace(',0','',$coord);
		$coord=str_replace("\n",'',$coord);
		$result[]=$coord;
	}
	return $result;
}

loadHabitatTab();

	echo '<pre>';
	print_r($habitatNames);
	echo '</pre>';

//echo $habitatNames[0];

$habitat = 'habitat.kml';

$xml = simplexml_load_file($habitat);

echo '<pre>';
print_r($xml);
echo '</pre>';

//$name=$xml->Document->name;
//$name=str_replace(' ','_',$name);
//echo 'var '.$name.' = [];<br>';

$names = array();
$count=0;
$json='';
foreach($xml->Folder->Placemark as $placemark) {
	//echo '________________________________________________________________________________';
	echo $count.' : ';
	$name=$placemark->name;
	$name=str_replace(' ','',$name);
	echo $name;
	if (!array_key_exists($name, $names)) {
		$names[$name]=array();
		//$names[]=$name;//]=array();
	}
	$names[$name][]=$count;

	echo '<pre>';
	print_r($habitatNames[$count]);
	//print_r($coords);
	echo '</pre>';

	//$tabname=$habitatNames[$count][1[

	//echo $name;
	echo '<br>';
	//echo $placemark->Polygon->outerBoundaryIs->LinearRing->coordinates;
	$coords=$placemark->Polygon->outerBoundaryIs->LinearRing->coordinates;
	$coords=processCoords($coords);
	echo '<pre>';
	print_r($coords);
	echo '</pre>';
	
	echo '<br>';

	$tabname = (isset($habitatNames[$count][1])) ? $habitatNames[$count][1] : $name;
	//$tabname = $habitatNames[$count];//)) ? $habitatNames[$count][1] : $name;
	$coordstr=implode('/',$coords);
	if ($json!='') $json.=',<br>';
	$json.='{ "name" : "'.$tabname.'", "coords" : "'.$coordstr.'"}';

	$count++;
} 

$habjson='';
foreach($habitatNames as $hab) {
	if ($habjson!='') $habjson.=',<br>';
	$habjson.='{ "id" : "'.$hab[0].'", "navn" : "'.$hab[1].'" }';
}

echo '<pre>';
print_r($names);
echo '</pre>';

echo $habjson;

echo $json;

?>
</body>
</html>



