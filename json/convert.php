<html>
<head>
<meta charset="UTF-8">
</head>
<body>

sfdsdfdsf

<?
error_reporting(E_ALL);
ini_set("display_errors", 1);

$json = file_get_contents('habitater_27062013.json');

//echo $json;

$json = json_decode($json);

$new = array();

foreach ($json->habitater as $habitat) {
	//echo $habitat->navn;
	$new[]=array(
		'id' => $habitat->id,
		'navn' => $habitat->navn
	);
}

echo '<pre>';
print_r($new);
echo '</pre>';

$json = json_encode($new);
file_put_contents('habitater_navne.json',$json, JSON_PRETTY_PRINT);
?>

