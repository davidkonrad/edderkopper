<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?
include('../common/Db.php');

class Lists extends Db {

	public function __construct() {
		parent::__construct();
		
		$get = (isset($_GET['get'])) ? $_GET['get'] : '';

		switch ($get) {
			case 'noid' : 
				$this->getNoId();
				break;

			default : 
				break;
		}
	}

	private function getNoId() {
		$json = file_get_contents('habitatnavne.json');
		$json = json_decode($json);

		mysql_set_charset('utf8');

		$list = array();
		foreach ($json as $item) {
			$SQL='select count(*) as c from habitat_kmlid_navne where navn="'.$item->navn.'"';
			$row=$this->getRow($SQL);
			if ($row['c']==0) {
				$list[]=$item->navn;
			}
		}
		asort($list);
		$html='<select size="20">';
		foreach ($list as $l) {
			$html.='<option>'.$l.'</option>';
		}
		$html.='</select>';
		echo $html;
	}

}

$list = new Lists();

?>
