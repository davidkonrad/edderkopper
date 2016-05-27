dfgdfgdfgdfg
<?

include('../../common/Db.php');

class Convert extends Db {

	public function __construct() {
		parent::__construct();
		$this->run();
	}

	private function run() {
		$SQL='select distinct s.SpeciesID, s.Species, g.Genus '.
			'from edderkopper_species s, edderkopper_genus g '.
			'where s.GenusID = g.GenusID';

		echo $SQL;

		$result = $this->query($SQL);

		$edderkopper = array();		

		while ($row = mysql_fetch_assoc($result)) {
			$name = $row['Genus'].' '.$row['Species'];
			//echo $name.'<br>';
			$edderkopper[$row['SpeciesID']]=$name;
		}

		mysql_select_db('allearter');
			
		$roed = array();

		foreach ($edderkopper as $id => $name) {
			$SQL='select den_danske_roedliste as r from allearter '.
				'where Videnskabeligt_navn="'.$name.'"';

			$row = $this->getRow($SQL);
			$roed[$id]=$row['r'];
			
			/*
			echo '<pre>';
			print_r($name.' -> '.$row['r']);
			echo '</pre>';
			*/
		}			
			
		echo '<pre>';
		print_r($roed);
		echo '</pre>';

		mysql_select_db('samlingerne');
		
		foreach ($roed as $id => $code) {		
			$SQL='update edderkopper_species set den_danske_roedliste="'.$code.'" '.
				'where SpeciesID='.$id;
			$this->exec($SQL);
		}			
	}
}

$test = new Convert();

?>


