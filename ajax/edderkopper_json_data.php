<?

include('../common/Db.php');

class JSON extends Db {

	public function __construct() {
		parent::__construct();

		header('Content-type: application/json; charset=utf-8');

		$target=(isset($_GET['target'])) ? $_GET['target'] : false;
		$table=false;$field=false;$id=false;

		if ($target) {
			switch ($target) {
				case 'genus' :
					$table='edderkopper_genus';
					$field='Genus';
					$id='GenusID';
					$this->getJSON($table, $field, $id);
					break;
				case 'family' :
					$table='edderkopper_family';
					$field='Family';
					$id='FamilyID';
					$this->getJSON($table, $field, $id);
					break;
				case 'species' :
					$this->lookupSpecies();
					break;
				default :
					return;
					break;
			}
		}
	}

	private function getJSON($table, $field, $id) {
		$SQL='select '.$field.','.$id.' from '.$table;
		$result=$this->query($SQL);
		$JSON='';
		while ($row = mysql_fetch_assoc($result)) {
			if ($JSON!='') $JSON.=',';
			$JSON.=' { "id" : "'.$row[$id].'", "value" : "'.$row[$field].'" } ';
		}
		$JSON='['.$JSON.']';
		echo $JSON;
	}


	private function lookupSpecies() {
		$SQL='select S.Species, S.SpeciesID, S.SAuthor, G.Genus '.
				'from edderkopper_species S, edderkopper_genus G '.
				'where S.GenusID=G.GenusID';

		$result=$this->query($SQL);
		$JSON='';
		while ($row = mysql_fetch_assoc($result)) {
			if ($JSON!='') $JSON.=',';
			$value=$row['Genus'].' '.$row['Species'].', '.$row['SAuthor'];
			$JSON.=' { "id" : "'.$row['SpeciesID'].'", "value" : "'.$value.'" } ';
		}
		$JSON='['.$JSON.']';
		echo $JSON;
	}

}
		
$json = new JSON();

?>
