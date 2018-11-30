<?
//debug
error_reporting(E_ALL);
ini_set('display_errors', '1');

include('../common/Db.php');

class Lookup extends Db {

	public function __construct() {
		parent::__construct();
	}

	//parses the the param "values=" passed by autocomplete.js
	//content filled by Search.getLookupValues()
	//return SQL field=value, based on "what"=content field name, "search"=table name
	protected function getExtra($what, $search) {
		if (!isset($_GET['values'])) return false;

		$extra=false;
		parse_str($_GET['values'], $a);
		if ($a[$what]!='') {
			$extra=$search.'="'.$a[$what].'"';
		}
		return $extra;
	}

	//return JSON [ { "value":"field", "text":"field"}, .. ]
	protected function getJSON($dataset, $field) {
		$json='';
		while ($row = $dataset->fetch(PDO::FETCH_ASSOC)) {
			if ($json!='') $json.=',';
			$json.='{"value" : "'.$row[$field].'", "text": "'.$row[$field].'"}';
		}
		$json='['.$json.']';
		return $json;
	}

}

?>
