<?
include('../common/Db.php');

class JSON extends Db {
	public $accepted = array();

	public function __construct() {
		parent::__construct();

		$kml = file_get_contents('habitatpolygoner.json');
		$kml = json_decode($kml);

		/*
		$id=0;
		$json='';
		foreach ($kml as $item) {
			if ($json!='') $json.=',';
			$json.=' "habitat" : ';
			$json.='{';
			$json.='"coords" : "'.$item->coords.'",';
			$json.='"id" : "'.$id.'",';

			$SQL='select navn from habitat_kmlid_navne where id='.$id;
			$row=$this->getRow($SQL);

			$json.='"navn" : "'.$row['navn'].'"';

			$json.='}';
			
			$id=$id+1;
		}
		*/

/*
		echo '['."<br>{";
		$id=0;
		$json='';
		foreach ($kml as $item) {
			if ($json!='') echo ",<br>";
			$json='.';
			
			echo ' "habitat" : ';
			echo '{';
			echo '"coords" : "'.$item->coords.'",'."<br>";;
			echo '"id" : "'.$id.'",'."<br>";;

			$SQL='select navn from habitat_kmlid_navne where id='.$id;
			$row=$this->getRow($SQL);

			echo '"navn" : "'.$row['navn'].'"'."<br>";;

			echo '}';
			
			$id=$id+1;
		}
		echo "<br>".'}]';
	}
*/
		echo '[ { "habitater" : [ '."<br>";
		$id=0;
		$json='';
		foreach ($kml as $item) {
			if ($json!='') echo ",<br>";
			$json='.';
			
			//echo ' "habitat" : ';
			echo '{';
			echo '"coords" : "'.$item->coords.'",'."<br>";;
			echo '"id" : "'.$id.'",'."<br>";;

			$SQL='select navn from habitat_kmlid_navne where id='.$id;
			$row=$this->getRow($SQL);

			echo '"navn" : "'.$row['navn'].'"'."<br>";;

			echo '}';
			
			$id=$id+1;
		}
		echo "<br>".']}]';
	}
}

$json = new JSON();


?>

